module.exports = {
	
	toMysqlFormat(date) {
		function twoDigits(d) {
			if(0 <= d && d < 10) return "0" + d.toString();
			if(-10 < d && d < 0) return "-0" + (-1*d).toString();
			return d.toString();
		}
		
		return date.getUTCFullYear() + "-" + twoDigits(1 + date.getUTCMonth()) + "-" + twoDigits(date.getUTCDate()) + " " + twoDigits(date.getUTCHours()) + ":" + twoDigits(date.getUTCMinutes()) + ":" + twoDigits(date.getUTCSeconds());
	},
	
	addSubscribeQueue(db, event){
		db.connection.query("SELECT * FROM depositEventsSubscribe WHERE contractAddress='"+event.returnValues._depositContractAddress+"' AND JSON_CONTAINS(events,'[\""+event.event+"\"]')", {}, function(err, result) {
			if(err==null) {
				result.forEach(function(item, index, result) {
					var sbscr = {
						'datetime':module.exports.toMysqlFormat(new Date()),
						'address':item.address, 
						'contractAddress': item.contractAddress,
						'email': item.email,
						'event': event.event,
						'sended': 0,
					}

					db.connection.query('INSERT INTO depositSubscribeQueue SET ?', sbscr, function(err, result) {})
			
				});
			}
		})	
	}
};

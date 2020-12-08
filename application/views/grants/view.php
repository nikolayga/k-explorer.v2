<div class="box-modal">
	<div class="card mb-0">
	  <div class="card-body px-1 py-1">
		 <table class="table table-bordered table table-striped table-sm">
			<tr><th>Grant ID</th><td><?=$grant['id']?></td></tr> 
			<tr><th>Grant Manager</th><td><div class="max-90p_"><a href="https://etherscan.io/address/<?=$grant['grantManager']?>" target="_blank"><?=substr($grant['grantManager'],0,6)?>...<?=substr($grant['grantManager'],strlen($grant['grantManager'])-6,6)?></a></div></td></tr> 
			<tr><th>Grantee</th><td><div class="max-90p_"><a href="https://etherscan.io/address/<?=$grant['grantee']?>" target="_blank"><?=substr($grant['grantee'],0,6)?>...<?=substr($grant['grantee'],strlen($grant['grantee'])-6,6)?></a></div></td></tr> 
			<tr><th>Staking Policy</th><td><div class="max-90p_"><a href="https://etherscan.io/address/<?=$grant['policy']?>" target="_blank"><?=substr($grant['policy'],0,6)?>...<?=substr($grant['policy'],strlen($grant['policy'])-6,6)?></a></div></td></tr> 
			<tr><th>Grant Amount</th><td><?=number_format($grant['amount'])?> KEEP</td></tr> 
			<tr><th>Grant Start</th><td><?=date("M d, Y",strtotime($grant['start']))?></td></tr> 
			<tr><th>Fully Unlocked At</th><td><?=date("M d, Y",strtotime($grant['end']))?></td></tr> 
			<tr><th>Duration</th><td><?=round($grant['duration']/30) ?> months</td></tr> 
			<tr><th>Withdrawn</th><td><?=number_format($grant['withdrawn'])?> KEEP</td></tr> 
			<tr><th>Withdrawable</th><td><?=number_format($grant['withdrawable'])?> KEEP</td></tr> 
			<tr><th>Staked</th><td><?=number_format($grant['staked'])?> KEEP</td></tr> 
			<tr><th>Cliff</th><td><?=date("M d, Y",strtotime($grant['cliff']))?></td></tr> 
			<tr><th>Revocable</th><td><?=$grant['revocable']==1?'yes':'no'?></td></tr>
			<?if($grant['revokedAt']):?>
			<tr><th>Revoked At</th><td><?=date("M d, Y",strtotime($grant['revokedAt']))?></td></tr>
			<tr><th>Revoked Amount</th><td><?=number_format($grant['revokedAmount'])?> KEEP</td></tr>
			<tr><th>Revoked Withdrawn</th><td><?=number_format($grant['revokedWithdrawn'])?> KEEP</td></tr>
			<?endif?>
		 </table>
		<a href="javascript:void(0)" class="btn btn-secondary btn-sm  arcticmodal-close float-right">Close</a>
	  </div>
	</div>
</div>


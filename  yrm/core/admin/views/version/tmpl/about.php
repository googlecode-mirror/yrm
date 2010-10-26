<?php defined('_JEXEC') or die('Restricted access'); ?>
<style type="text/css">
#yos_about {
	width:80%;
	overflow:hidden;	
	color: #4C608B;
	text-align: left;
	margin: 0;	
	font-size: 11px;
	font-weight: normal;
	font-family: 'Andale Mono', sans-serif;
	
}

#title_about {	
	font-weight: bold;
	font-size: 20px;
	overflow:hidden;
	text-align: center;
	color: #1A315A;
	padding: 3px;	
	height:25px;
}

#content_about {
	padding: 10px;
}

#developer_about {
	padding: 10px;
}
</style>

<div align="center">
<div id="yos_about">
	<div id="title_about">
		YOS Resources Manager
	</div>
	<div id="content_about">
		<p style="text-align: center;">
			<img src="components/com_yos_resources_manager/assets/images/yrm.jpg"/>
		</p>
	</div>
	<div id="developer_about">
		<b>Developers</b>: <br/>
		<ul>
			<font style="font-weight:normal;">
			<li><a href="mailto:minhna@f5vietnam.com">Nguyen Anh Minh</a></li>
			<li></strong> <a href="mailto:cuongpm@f5vietnam.com">Phan Manh Cuong</a></li>
			<li></strong> <a href="mailto:kiennv@f5vietnam.com">Nguyen Van Kien</a></li>
			<li></strong> <a href="mailto:ducdm@f5vietnam.com">Dam Manh Duc</a></li>
			</font>
		</ul>
		<p><b>Info:</b></p>
		<font style="font-weight:normal;">		
		Version: <b><?php echo $this->get('Version'); ?></b><br/>
		<?php
		if (isset($this->checkversion)) {
			if ($this->checkversion) {
				echo '<a href="http://www.yopensource.com/"><font color="red">New version is available!</font></a>&nbsp;&nbsp;&nbsp;';
				echo '<a title="Upgrade" href="index.php?option=com_yos_resources_manager&amp;task=upgrade.doupdate&amp;version='.$this->get('Version').'&amp;url='.$this->get('URL').'&amp;pc='.$this->get('PC').'"><b>Upgrade Now!</b></a>';
				echo '&nbsp;&nbsp;&nbsp;<a title="Backup" href="index.php?option=com_yos_resources_manager&amp;task=upgrade.getbackup&amp;version='.$this->get('Version').'&amp;url='.$this->get('URL').'&amp;pc='.$this->get('PC').'"><b>Undo Upgrade Now!</b></a>';
				echo '&nbsp;&nbsp;&nbsp;<a title="Get Backup File" href="index.php?option=com_yos_resources_manager&amp;task=upgrade.getFileBackup"><b>Get Backup File!</b></a>';
				
			} else {
				echo 'Your version is latest!';
				echo '&nbsp;&nbsp;&nbsp;<a title="Backup" href="index.php?option=com_yos_resources_manager&amp;task=upgrade.getbackup&amp;version='.$this->get('Version').'&amp;url='.$this->get('URL').'&amp;pc='.$this->get('PC').'"><b>Undo Upgrade Now!</b></a>';
				echo '&nbsp;&nbsp;&nbsp;<a title="Get Backup File" href="index.php?option=com_yos_resources_manager&amp;task=upgrade.getFileBackup"><b>Get Backup File!</b></a>';
			}
		}
		else {
		?>
		<p>
			<a href="index.php?option=com_yos_resources_manager&view=version&checknow=1" title="">Click here to check for updates</a>
		</p>
		<?php
		}	
		?>
		<p>
			<b>Copyright</b>: &copy; 2009 YOS.,JSC. All rights reserved.<br />
			<b>License</b>: GNU/GPL.	
		</p>
		</font>
	</div>
</div>
</div>
	

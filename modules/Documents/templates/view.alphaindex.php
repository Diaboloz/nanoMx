
<a name="contenthome" ></a>

<div class="postcontent" ><h2><?php echo $this->title ?></h2>
<p class="align-right tiny"><?php echo $this->adminlink ?></p>
</div><hr /> 
<div class="opentable" style="height:auto;">
<?php echo $this->text; 
$books= $this->doc; 
?>
<div class="title align-center">
<?php   echo $this->alphaindex; ?>
</div>
	<h3><?php echo $this->char ?></h3>
	<div class="postcontent">
		<?php
				foreach ($this->ilist as $cats)
				{
					echo "<li class=\"mainpage\" style=\"width: ".$this->width."%\"><div class=\"blogstory\">";
					$book=$books->getBookRoot($cats['id']);
					$bookstitle=$book['title'];
					echo "<p><span class=\"tiny\"><a href=\"modules.php?name=$this->module_name&amp;act=page&amp;id=".$book['id']."\">".$bookstitle."</a></span>";

					?>
					</div></li>
				<?php }	?>
				
	</ul></div>

 </div>
	<div class="align-right">
		<a class="button" href="#contenthome"  title="<?php echo _HOME ?>" ><img src="images/up.gif" alt="up" /></a>
	</div>

<div class="clear"></div>

<?php /* pragmaMx CVS $Id: view.alphaindex.php 171 2016-06-29 11:59:03Z PragmaMx $ */ ?>
<div class="container">
	<div class="row">
		<div class="col ">
			<h1><?= _('Edit observation scheme');?></h1>
		</div>
	</div>
	<div class="row">
		<div class="col ">
			<label for="osname"><?=_('Observation Scheme Name');?></label>
			<input type="text" class="form-control" name="name" id="osname">
		</div>
		<div class="col ">
			<label for="osdescr" ><?=_('Observation Scheme Description');?></label>
			<textarea name="description" class="form-control" id="osdescr"></textarea>
		</div>
		<div class="col ">
			<label for="osref"><?=_('Reference');?></label>
			<input type="text" class="form-control" name="osref" id="osref">
			<small class="text-muted"><?= _('Recommended quote when using public observation schemes.');?></small>
		</div>
	</div>
	<div class="row">
		<div class="col ">
			<button id="saveobsscheme" class="btn btn-observe float-right"><?=_('Save Observation Scheme');?></button>
		</div>
	</div>
</div>
<div class="container-fluid">
	<div class="row justify-content-center p-3">
		<div class="col-auto">
<!-- 			<label for="obsscheme"><?=_('Design Observation Scheme');?></label> -->
			<div id="obsscheme" class="container-fluid obsscheme"></div>
		</div>
	</div>
</div>
<div class="container">
	<div class="row justify-content-center">
		<div class="col-auto">
			<button class="btn btn-observe float-right" data-toggle="modal" data-target="#quickfill"><?=_('Quick-fill');?></button>
			<small class="text-muted"><?= _('Use TAB to go to the next input. Hit Enter to stop writing. Hit Shift+Enter to open a new value after this one. Hit Esc to cancel.');?></small><br>
			<small class="text-muted"><?= _('Row and column names are optional. Use them to support the observer. Or leave them blank.');?></small><br>
			<small class="text-muted"><?= _('Click <i class="fas fa-plus-circle"></i> to add values, categories, columns and rows.');?></small>
			<small class="text-muted"><?= _('Use the mouse to reorder (drag and drop) values, categories, columns and rows.');?></small><br>
			<small class="text-muted"><?= _('Click <i class="fas fa-check"></i> to make it possible to make more than one choice. ');?></small>
			<small class="text-muted"><?= _('Click <i class="fas fa-hand-holding"></i> to make it mandatory to register a choice in this category.');?></small>
			<small class="text-muted"><?= _('Click <i class="fas fa-palette"></i> to change the color of the category.');?></small>
			<small class="text-muted"><?= _('Click <i class="fas fa-key"></i> to make the visibility of the category dependent on the choice of value in another category.');?></small><br>
			<small class="text-muted"><?= _('Click <i class="fas fa-trash"></i> to delete rows, columns, categories and values.');?></small>
		</div>
	</div>
</div>
<div class="container-fluid" id="observe"></div>
<div class="modal" id="dependencymodal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?php _('Dependencies');?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="dependencymodalbody">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-observe" id="adddependency"><?=_('Add dependency');?></button>
        <button type="button" class="btn btn-observe" data-dismiss="modal"><?=_('Close');?></button>
      </div>
    </div>
  </div>
</div>
<div class="modal" id="quickfill" tabindex="-1" role="dialog" aria-labelledby="quickfill" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?php _('Quickly create categories and values');?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="">
        <textarea class="form-control" rows="20" id="quickdata"></textarea>
		<p><small class="text-muted"><?= _('Create new categories and values by writing one category on each line on for form: categoryname:value1;value2;...;valueN');?></small><br>
		<small class="text-muted"><?= _('The new categories will be inserted into the end of the last column of the observation scheme.');?></small></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-observe" id="quickcreate"><?=_('Create');?></button>
        <button type="button" class="btn btn-observe" data-dismiss="modal"><?=_('Close');?></button>
      </div>
    </div>
  </div>
</div>
<?php 
if($id) {
	$q="select * from obsschemes where id=".$id;
	$result=$mysqli->query($q);
	$r=$result->fetch_array();
	$log.=$q;
	$res=array("obsid"=>$r["id"],"obsscheme"=>json_decode($r["obsscheme"]),"name"=>$r["name"],"description"=>$r["description"],"reference"=>$r["reference"],"log"=>$log);
} else $res=array("obsid"=>0,"obsscheme"=>"","name"=>"","description"=>"","log"=>$log);

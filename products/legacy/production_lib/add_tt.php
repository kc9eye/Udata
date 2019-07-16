<?php
   /*
   Copyright 2009-2014 Paul W.Lane <kc9eye@gmail.com>
   This file is part of chicago-assy intranet.

   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   as published by the Free Software Foundation; either version 2
   of the License.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
   */
require_once(dirname(__DIR__).'/legacy_init.php');
$view = $server->getViewer("Legacy Products");
$view->PageData['headinserts'] = [
    '<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"></script>',
    '<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.11.4/themes/vader/jquery-ui.css" />'
];
$view->sideDropDownMenu($submenu);

   // These need to be changed for subsequent runs.
   ///////////////////////////////////////////////
   $MODEL_RUN = "1";
   $MODEL_YEAR = "2018";
   $MODEL_PREFIX = "TT";
   $MODEL_SUFFIX = "J";
   //////////////////////////////////////////////

?>
<script type="text/javascript">
   $(document).ready(function(){
      confSubmit = false;
      _special = /[(@#$%^&*`~)]/gi;
      $("#numbers_only").dialog({
         modal:true, autoOpen:false, buttons:{Ok:function(){
            $(this).dialog("close");return false;}}});
      $("#special_chars").dialog({
         modal:true,autoOpen:false,buttons:{Ok:function(){
            $(this).dialog("close");return false;}}});
      $("#field_required").dialog({
         modal:true,autoOpen:false,buttons:{Ok:function(){
            $(this).dialog("close");return false;}}});
      $("#number_much").dialog({
         modal:true,autoOpen:false,buttons:{Ok:function(){
            $(this).dialog("close");return false;}}});
      $("#confirm").dialog({
         modal:true,autoOpen:false,buttons:{
            "Submit":function(){
               confSubmit = true;
               $(this).dialog("close");
               $("#build_entry").submit();},
            "Cancel":function(){
               $(this).dialog("close");
               return false;
               }}});
      $("#build_entry").submit(function(form){
         if (! confSubmit) {form.preventDefault();}
         else {return true;}
         $(":input").each(function(){
            if (_special.test($(this).val())) {
               $(this).css("background-color","yellow");
               $("#special_chars").dialog("open");
               exit();
               }});
         $("input[type='text']").each(function(){
            if ($(this).val() == "") {
               $(this).css("background-color","red");
               $("#field_required").dialog("open");
               exit();
               }});
         $("#confirm").dialog("open");
         });
      });
</script>
<div id="right_data_display">
   <div id="numbers_only" title="Entry Error">Only numbers allowed in this field.</div>
   <div id="special_chars" title="Entry Error">No special characters allowed in this field.</div>
   <div id="field_required" title="Entry Error">Missing required field.</div>
   <div id="confirm" title="Confirm Submission">
      Are you sure you want to add this unit to the buildsheet?
   </div>
   <h2><?php echo "{$MODEL_YEAR} {$MODEL_PREFIX}-{$MODEL_SUFFIX}";?> Build Checklist</h2>
   <form id="build_entry" action="/testing/products/legacy/production_lib/add_truck_func" method="post">
      <input type="hidden" name="model_year" value="<?php echo $MODEL_YEAR;?>" />
      <input type="hidden" name="model_prefix" value="<?php echo $MODEL_PREFIX;?>" />
      <input type="hidden" name="model_suffix" value="<?php echo $MODEL_SUFFIX;?>" />
      <input type="hidden" name="run_num" value="<?php echo $MODEL_RUN;?>" />
      <input type="hidden" name="reporter_id" value="<?php echo sql_reporter_id();?>" />
      <table class="data_entry">
         <tr>
            <th>Truck Number:</th>
            <td><?php echo $MODEL_PREFIX;?><input name="number" size="4" type="text" /><?php echo $MODEL_SUFFIX;?></td>
         </tr>
         <tr>
            <th>License Info:</th>
            <td><input type="text" size="10" name="license"  /> AZ</td>
         </tr>
         <tr>
            <th>Super Graphic State:</th>
            <td>
               <select name="graphic">
               <option value="AL">Alabama</option>
               <option value="AK">Alaska</option>
               <option value="AZ">Arizona</option>
               <option value="AR">Arkansas</option>
               <opiton value="CA">California</option>
               <option value="CO">Colorado</option>
               <option value="CT">Connecticut</option>
               <option value="DE">Delaware</option>
               <option value="DC">Dis. Columbia</option>
               <option value="FL">Florida</option>
               <option value="GA">Georgia</opiton>
               <option value="HI">Hawaii</option>
               <option value="ID">Idaho</option>
               <option value="IL">Illinois</option>
               <option value="IN">Indiana</option>
               <option value="IA">Iowa</option>
               <option value="KS">Kansas</option>
               <option value="KY">Kentucky</option>
               <option value="LA">Louisiana</option>
               <option value="ME">Maine</option>
               <option value="MD">Maryland</option>
               <option value="MA">Massachusetts</option>
               <option value="MN">Minnesota</option>
               <option value="MS">Mississippi</option>
               <option value="MO">Missoui</option>
               <option value="MT">Montana</option>
               <option value="NE">Nebraska</option>
               <option value="NV">Nevada</option>
               <option value="NH">New Hampshire</option>
               <option value="NJ">New Jersey</option>
               <option value="NM">New Mexico</option>
               <option value="NY">New York</option>
               <option value="NC">North Carolina</option>
               <option value="ND">North Dakota</option>
               <option value="OH">Ohio</option>
               <option value="OK">Oklahoma</option>
               <option value="OR">Oregon</option>
               <option value="PA">Pennsylvania</option>
               <option value="RI">Rhode Island</option>
               <option value="SC">South Carolina</option>
               <option value="SD">South Dakota</option>
               <option value="TN">Tennessee</option>
               <option value="TX">Texas</option>
               <option value="UT">Utah</option>
               <option value="VT">Vermont</option>
               <option value="VA">Virginia</option>
               <option value="WA">Washington</option>
               <option value="WV">West Virginia</option>
               <option value="WI">Wisconsin</option>
               <option value="WY">Wyoming</option>
               <option value="AB">Alberta</option>
               <option value="BC">British Columbia</option>
               <option value="MB">Manitoba</option>
               <option value="NB">New Brunswick</option>
               <option value="NL">Newfoundland &amp; Labrador</option>
               <option value="NS">Nova Scotia</option>
               <option value="NT">Northwest Territories</option>
               <option value="NU">Nunavut</option>
               <option value="ON">Ontario</option>
               <option value="PE">Prince Edward Is.</option>
               <option value="QC">Quebec</option>
               <option value="SK">Saskatchewan</option>
               <option value="YT">Yukon</option>
               </select>
            </td>
         </tr>
      </table>
      <p>Once complete click "Add Truck":<br />Clicking now enters truck at 100% with no comments.</p>
      <input type="submit" value="Add Unit" />&#160;<input type="reset" value="Reset" />
      <hr />
      <div id="accordion">
         <h3>PDI Section</h3>
         <div id="pdi">
            <table class="data_entry">
               <tr>
                  <th>Receiving/Disassembly:</th>
                  <td>OK:<input type="radio" name="prepdi" value="t" checked="checked" />&#160;&#160;&#160;Defect:<input type="radio" name="prepdi" value="f" /></td>
               </tr>
               <tr>
                  <th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="prepdi_com"></textarea></td>
               </tr>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>Bumpers:</th>
                  <td>OK:<input type="radio" name="bumper" value="t" checked="checked" />&#160;&#160;&#160;Defect<input type="radio" name="bumper" value="f" /></td>
               </tr>
               <tr>
                  <th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="bumper_com"></textarea></td>
               </tr>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>Stretch:</th>
                  <td>OK:<input type="radio" name="stretch" value="t" checked="checked" />&#160;&#160;&#160;Defect:<input type="radio" name="stretch" value="f" /></td>
               </tr>
               <tr>
                  <th>Comment:</th>
                  <td><textarea cols="50" rows="2" name="stretch_com"></textarea></td>
               </tr>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>Diamond Plates:</th>
                  <td>OK:<input type="radio" name="diamond" value="t" checked="checked" />&#160;&#160;&#160;Defect:<input type="radio" name="diamond" value="f" /></td>
               </tr>
               <tr>
                  <th>Comment:</th>
                  <td><textarea cols="50" rows="2" name="diamond_com"></textarea></td>
               </tr>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>Frame Stiffeners:</th>
                  <td>OK:<input type="radio" name="stiffener" value="t" checked="checked" />&#160;&#160;&#160;Defect:<input type="radio" name="stiffener" value="f" /></td>
               </tr>
               <tr>
                  <th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="stiffener_com"></textarea></td>
               </tr>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>PDI/Reassembly:</th>
                  <td>OK:<input type="radio" name="pdireass" value="t" checked="checked" />&#160;&#160;&#160;Defect:<input type="radio" name="pdireass" value="f" /></td>
               </tr>
               <tr>
                  <th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="pdireass_com"></textarea></td>
               </tr>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>Cab Extension:</th>
                  <td>OK:<input type="radio" name="cabext" value="t" checked="checked" />&#160;&#160;&#160;Defect:<input type="radio" name="cabext" value="f" /></td>
               </tr>
               <tr>
                  <th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="cabext_com"></textarea></td>
               </tr>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>Tires:</th>
                  <td>OK:<input type="radio" name="tire" value="t" checked="checked" />&#160;&#160;&#160;Defect:<input type="radio" name="tire" value="f" /></td>
               </tr>
               <tr>
                  <th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="tire_com"></textarea></td>
               </tr>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>Seat Install:</th>
                  <td>OK:<input type="radio" name="seat" value="t" checked="checked" />&#160;&#160;&#160;Defect:<input type="radio" name="seat" value="f" /></td>
               </tr>
               <tr>
                  <th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="seat_com"></textarea></td>
               </tr>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>Subframes:</th>
                  <td>OK:<input type="radio" name="sub" value="t" checked="checked" />&#160;&#160;&#160;Defect:<input type="radio" name="sub" value="f" /></td>
               </tr>
               <tr>
                  <th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="sub_com"></textarea></td>
               </tr>
            </table>
         </div>
         <h3>Marriage Section</h3>
         <div id="marriage">
            <hr />
            <table class="data_entry">
               <tr>
                  <th>Marriage:</th>
                  <td>OK:<input type="radio" name="marriage" value="t" checked="checked" />&#160;&#160;&#160;Defect:<input type="radio" name="marriage" value="f" /></td>
               </tr>
               <tr><th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="marriage_com"></textarea></td>
               </tr>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>Ramp Install:</th>
                  <td>OK:<input type="radio" name="ramp" value="t" checked="checked" />&#160;&#160;&#160;Defect:<input type="radio" name="ramp" value="f" /></td>
               </tr>
               <tr><th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="ramp_com"></textarea></td>
               </tr>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>Skirt Install:</th>
                  <td>OK:<input type="radio" name="skirt" value="t" checked="checked" />&#160;&#160;&#160;Defect:<input type="radio" name="skirt" value="f" /></td>
               </tr>
               <tr><th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="skirt_com"></textarea></td>
               </tr>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>Wiring:</th>
                  <td>OK:<input type="radio" name="wiring" value="t" checked="checked" />&#160;&#160;&#160;Defect:<input type="radio" name="wiring" value="f" /></td>
               </tr>
               <tr><th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="wiring_com"></textarea></td>
               </tr>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>Caulk:</th>
                  <td>OK:<input type="radio" name="caulk" value="t" checked="checked" />&#160;&#160;&#160;Defect:<input type="radio" name="caulk" value="f" /></td>
               </tr>
               <tr><th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="caulk_com"></textarea></td>
               </tr>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>Cab Decal:</th>
                  <td>OK:<input type="radio" name="cabdecal" value="t" checked="checked" />&#160;&#160;&#160;Defect:<input type="radio" name="cabdecal" value="f" /></td>
               </tr>
               <tr>
                  <th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="cabdecal_com"></textarea></td>
               </tr>
            </table>
         </div>
         <h3>Box Section</h3>
         <div id="box">
            <table class="data_entry">
               <tr>
                  <th>Decks:</th>
                  <td>OK:<input type="radio" name="deck" value="t" checked="checked" />&#160;&#160;&#160;Defect<input type="radio" name="deck" value="f" /></td>
               </tr>
               <tr>
                  <th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="deck_com"></textarea></td>
               </tr>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>Board Up:</th>
                  <td>OK:<input type="radio" name="boardup" value="t" checked="checked" />&#160;&#160;&#160;Defect<input type="radio" name="boardup" value="f" /></td>
               </tr>
               <tr>
                  <th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="boardup_com"></textarea></td>
               </tr>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>Wall Decal:</th>
                  <td>OK:<input type="radio" name="walldecal" value="t" checked="checked" />&#160;&#160;&#160;Defect<input type="radio" name="walldecal" value="f" /></td>
               </tr>
               <tr>
                  <th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="walldecal_com"></textarea></td>
               </tr>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>Luten Build:</th>
                  <td>OK:<input type="radio" name="lutenbuild" value="t" checked="checked" />&#160;&#160;&#160;Defect<input type="radio" name="lutenbuild" value="f" /></td>
               </tr>
               <tr>
                  <th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="lutenbuild_com"></textarea></td>
               </tr>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>Luten Install:</th>
                  <td>OK:<input type="radio" name="luteninstall" value="t" checked="checked" />&#160;&#160;&#160;Defect<input type="radio" name="luteninstall" value="f" /></td>
               </tr>
               <tr>
                  <th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="luteninstall_com"></textarea></td>
               </tr>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>Rubrails:</th>
                  <td>OK:<input type="radio" name="rubrail" value="t" checked="checked" />&#160;&#160;&#160;Defect<input type="radio" name="rubrail" value="f" /></td>
               </tr>
               <tr>
                  <th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="rubrail_com"></textarea></td>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>Roof Build:</th>
                  <td>OK:<input type="radio" name="roofbuild" value="t" checked="checked" />&#160;&#160;&#160;Defect<input type="radio" name="roofbuild" value="f" /></td>
               </tr>
               <tr>
                  <th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="roofbuild_com"></textarea></td>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>Roof Install:</th>
                  <td>OK:<input type="radio" name="roofinstall" value="t" checked="checked" />&#160;&#160;&#160;Defect<input type="radio" name="roofinstall" value="f" /></td>
               </tr>
               <tr>
                  <th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="roofinstall_com"></textarea></td>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>Door Decal:</th>
                  <td>OK:<input type="radio" name="doordecal" value="t" checked="checked" />&#160;&#160;&#160;Defect<input type="radio" name="doordecal" value="f" /></td>
               </tr>
               <tr>
                  <th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="doordecal_com"></textarea></td>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>Door Build:</th>
                  <td>OK:<input type="radio" name="doorbuild" value="t" checked="checked" />&#160;&#160;&#160;Defect<input type="radio" name="doorbuild" value="f" /></td>
               </tr>
               <tr>
                  <th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="doorbuild_com"></textarea></td>
                </tr>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>Door Install:</th>
                  <td>OK:<input type="radio" name="doorinstall" value="t" checked="checked" />&#160;&#160;&#160;Defect<input type="radio" name="doorinstall" value="f" /></td>
               </tr>
               <tr>
                  <th>Comment:</th>
                  <td><textarea  cols="50" rows="2" name="doorinstall_com"></textarea></td>
               <tr><th colspan="2"><hr /></th></tr>
               <tr>
                  <th>Small Decals:</th>
                  <td>OK:<input type="radio" name="smalldecal" value="t" checked="checked" />&#160;&#160;&#160;Defect<input type="radio" name="smalldecal" value="f" /></td>
               </tr>
               <tr>
                  <th>Comment:</th><td><textarea  cols="50" rows="2" name="smalldecal_com"></textarea></td>
               </tr>
            </table>
         </div>
      </div>
   </form>
</div>
<?php
   $view->footer();
?>

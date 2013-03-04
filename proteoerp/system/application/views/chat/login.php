<div id="loginform">
<?php echo form_open('login/submit'); ?>
<p>Por favor introdusca su nombre para continuar:</p>
<label for="name">Name:</label>
<input type="text" name="name" id="name" />
<input type="submit" name="enter" id="enter" value="Enter" />
</form>
</div>
<style>
*{margin:0;padding:0; text-align:center;}
#loginform {
margin:0 auto;
padding-bottom:25px;
background:#EBF4FB;
border:1px solid #ACD8F0; }

#loginform { padding-top:18px; }

#loginform p { margin: 5px; }
</style>
<?php /* Smarty version Smarty-3.1-DEV, created on 2013-07-23 15:44:08
         compiled from "smarty.html" */ ?>
<?php /*%%SmartyHeaderCode:43316183151ee43c550cde6-78091750%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f9905d399e5931b5a4669e55db876ad7c615ed60' => 
    array (
      0 => 'smarty.html',
      1 => 1374583447,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '43316183151ee43c550cde6-78091750',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1-DEV',
  'unifunc' => 'content_51ee43c5534f61_20107040',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51ee43c5534f61_20107040')) {function content_51ee43c5534f61_20107040($_smarty_tpl) {?><?php echo smarty__(array('original'=>'test'),$_smarty_tpl);?>


<div><?php echo smartyn__(array('original'=>'%d apple','plural'=>"%d apples",'count'=>'4'),$_smarty_tpl);?>
</div>

<div><?php echo smartyn__(array('count'=>4,'original'=>'%d plum','plural'=>"%d plums",'dsad'=>213),$_smarty_tpl);?>
</div>
<div><?php echo smartyn__(array('count'=>5,'original'=>'%d plum','plural'=>"%d plums",'dsad'=>213),$_smarty_tpl);?>
</div>
<div><?php echo smartyn__(array('count'=>4,'original'=>'%d plum','plural'=>"%d plumZ",'dsad'=>213),$_smarty_tpl);?>
</div>
<div><?php echo smartynp__(array('count'=>2,'original'=>'%d plum with context','plural'=>"%d plumZ with context",'context'=>'male'),$_smarty_tpl);?>
</div>

wqewq
sadas<?php }} ?>
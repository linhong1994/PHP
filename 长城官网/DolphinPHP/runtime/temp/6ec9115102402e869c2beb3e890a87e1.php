<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:59:"E:\WWW\DolphinPHP/application/index\view\index\publish.html";i:1506743287;s:52:"E:\WWW\DolphinPHP/application/index\view\layout.html";i:1506743479;}*/ ?>
<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title>长城信息-让靠谱的工程师为您开发|基于云技术的软件外包服务平台</title>
  <meta name="keywords" content="基于云技术的软件外包服务平台。服务范围：微信、H5、APP、产品策划及开发">
	<meta name="description" content="基于云技术的软件外包服务平台。服务范围：微信、H5、APP、产品策划及开发">
  <meta name="author" content="templatemo">
    
    
	<!-- Google Fonts 
	<link href="http://fonts.useso.com/css?family=PT+Serif:400,700,400italic,700itali" rel="stylesheet">
	<link href="http://fonts.useso.com/css?family=Raleway:400,900,800,700,500,200,100,600" rel="stylesheet">-->

	<!-- Stylesheets -->
	<link rel="stylesheet" href="__STATIC__/index/bootstrap/bootstrap.css">
	<link rel="stylesheet" href="__STATIC__/index/css/misc.css">
	<link rel="stylesheet" href="__STATIC__/index/css/blue-scheme.css">
	
	<!-- JavaScripts -->
	<script src="__STATIC__/index/js/jquery-1.10.2.min.js"></script>
	<script src="__STATIC__/index/js/jquery-migrate-1.2.1.min.js"></script>

	<link rel="shortcut icon" href="__STATIC__/index/images/favicon.ico" type="image/x-icon" />

</head>
<style>
.fa-2x {font-size:4em}
.index-user { text-align:center;}
</style>
<body>
	<div class="responsive_menu">
        <ul class="main_menu">
            <li><a href="<?php echo url('index'); ?>">首页</a></li>
            <li><a href="<?php echo url('project'); ?>">案例</a></li>
            <li><a href="<?php echo url('blog'); ?>">博客</a></li>
        </ul> <!-- /.main_menu -->
    </div> <!-- /.responsive_menu -->

	<header class="site-header clearfix">
		<div class="container">

			<div class="row">

				<div class="col-md-12">

					<div class="pull-left logo">
						<a href="<?php echo url('index'); ?>">
              <?php if(!(empty(\think\Config::get('web_site_logo')) || ((\think\Config::get('web_site_logo') instanceof \think\Collection || \think\Config::get('web_site_logo') instanceof \think\Paginator ) && \think\Config::get('web_site_logo')->isEmpty()))): ?>
              <img src="<?php echo get_file_path(\think\Config::get('web_site_logo')); ?>" alt="长城信息">
              <?php else: ?>
              <img src="__STATIC__/index/images/logo.png" alt="长城信息">
              <?php endif; ?>
						</a>
					</div>	<!-- /.logo -->

					<div class="main-navigation pull-right">

						<nav class="main-nav visible-md visible-lg">
							<ul class="sf-menu">
								<li><a href="<?php echo url('index'); ?>">首页</a></li>
                                <li><a href="<?php echo url('project'); ?>">案例</a></li>
                                <li><a href="<?php echo url('blog'); ?>">博客</a></li>
                                <li><a href="<?php echo url('publish'); ?>" class="main-button accent-color">提交需求<i class="icon-button fa fa-arrow-right"></i></a><li>
							</ul> <!-- /.sf-menu -->
						</nav> <!-- /.main-nav -->

						<!-- This one in here is responsive menu for tablet and mobiles -->
					    <div class="responsive-navigation visible-sm visible-xs">
					        <a href="#nogo" class="menu-toggle-btn">
					            <i class="fa fa-bars"></i>
					        </a>
					    </div> <!-- /responsive_navigation -->

					</div> <!-- /.main-navigation -->

				</div> <!-- /.col-md-12 -->

			</div> <!-- /.row -->

		</div> <!-- /.container -->
	</header> <!-- /.site-header -->


  
	


	<div class="first-widget parallax" id="blog">
		<div class="parallax-overlay">
			<div class="container pageTitle">
				<div class="row">
					<div class="col-md-12 col-sm-12 text-center" style="margin-bottom:20px;">
						<h2 class="page-title">项目需求</h2>
					</div> <!-- /.col-md-6 -->
					<div class="col-md-12 col-sm-12 text-center">
						<span class="page-location">为您的需求提供快速可靠的技术解决方案</span>
					</div> <!-- /.col-md-6 -->
				</div> <!-- /.row -->
			</div> <!-- /.container -->
		</div> <!-- /.parallax-overlay -->
	</div> <!-- /.pageTitle -->

<style>
/* 定义手机屏幕或者小于600px的屏幕  Use a media query to add a breakpoint at 768px */
@media only screen and (max-width: 600px) {
 .label-cell, .input-cell  {
	  display:block;
    }
.publish-form input.block-1P1yJ { width:100%;}
.process-container-3iNBd { display:none}
}
/*定义全屏或者768px以上屏幕宽度样式*/
@media screen and (min-width:768px) {
	.label-cell, .input-cell  {
	  display: table-cell;
    }
}
body, html { font-size:14px;}
.publish-form { }
.form-header { color:#979fa8; font-size:1.2rem; padding:2rem 0; text-align:center; border-bottom:1px solid hsla(212,9%,63%,.2)}
.form-header strong { font-weight:400; color:#000;}
.form-row { padding:2rem 0; width:100%; border-bottom:1px solid hsla(212,9%,63%,.2)}
.label-cell { vertical-align:top;}
.cell-body { width:10rem; box-sizing:border-box;}
.cell-body label { font-size:1.2rem; color:#2d3238; display: block; padding:0.5rem 0;}
.input-cell { width:100%; vertical-align:middle;}
.publish-form input.block-1P1yJ { background:#edf1f5; display:block; width:100%;}
.publish-form input { border:none; background:#eef1f5; padding:0.6rem 1rem; outline:none; font-size:1rem; color:#2d3238; border-radius:5px; box-sizing:border-box; border:1px solid #cad3de;}
.error-vXvqJ { font-size:12px; color:#db5858; line-height:1.5rem;}
.error-vXvqJ i.fa { color:#db5858; font-size:1rem; margin-right:.3rem;}
.fa { display:inline-block;}
.fa-times-circle:before {content:"\F057"}
textarea.ui-textarea-1NZvv { outline:none;border-radius:4px; border:1px solid #cad3de; padding:0.8rem; 1rem;
color:#2d3238; background:#fff; box-sizing:border-box; width:100%; font-size:1rem;}
.btn-container-1rScq { margin-bottom:1rem; text-align:center;}
.btn-container-1rScq button { outline:none; border-radius:4px; padding:0.75rem; width:15rem; text-align:center;
background:#4289dc; color:#fff; border:none;  font-size:1rem; cursor:pointer;
transition:all 150ms ease-in}
.btn-container-1rScq:hover button { background:#7DA1DA;}
.blog-posts { margin-top:30px}
.sidebar { margin-top:30px;}
.publish-form select { outline:none; border:1px solid #cad3de; padding:0.6rem 1rem; background:#edf1f5 none;
border-radius:0.4rem; height:3rem; line-height:1; font-size:1rem; color:#2d3238; max-width:8rem; box-sizing:border-box;}
.publish-form input.id-botton-2 { border:1px solid #4289DC}
</style>
	<div class="container">	
		<div class="row">

			<div class="col-md-8 blog-posts">
				<form action="<?php echo url('publish'); ?>" method="post" >
                 <div class="row">
                    <div class="publish-form col-md-12">
                      <div class="form-row">
                          <div class="label-cell">
                             <div class="cell-body">
                                <label>姓名</label>
                             </div>
                          </div>
                          <div class="input-cell" id="name">
                             <div class="cell-body-ue4f1">
                                <input type="text" class="block-1P1yJ" maxlength="30" placeholder="请填写您的称呼" name="name" value="" required="required">
                             </div>

                          </div>
                      </div>
                      <div class="form-row">
                          <div class="label-cell">
                             <div class="cell-body">
                                <label>电话</label>
                             </div>
                          </div>
                          <div class="input-cell" id="tel">
                             <div class="cell-body-ue4f1">
                                <input type="number" class="block-1P1yJ" maxlength="12" placeholder="请填写您的联系电话" name="tel" value="" required="required">
                             </div>

                          </div>
                      </div>
                      

                      <div class="form-row">
                          <div class="label-cell">
                             <div class="cell-body">
                                <label>QQ/微信</label>
                             </div>
                          </div>
                          <div class="input-cell" id="qq">
                             <div class="cell-body-ue4f1">
                                <input type="text" class="block-1P1yJ" maxlength="30" placeholder="请填写您的QQ/微信" name="qq" value="" required="required">
                             </div>

                          </div>
                      </div>
                      
                      <div class="form-row">
                          <div class="label-cell">
                             <div class="cell-body">
                                <label>需求描述</label>
                             </div>
                          </div>
                          <div class="input-cell" id="demand">
                             <div class="cell-body-ue4f1">
                                <textarea class=" ui-textarea-1NZvv ui-auto-size-2NZ-W" rows="6" name="demand" placeholder="示例：我想做一款App，iOS 和Android两个平台，现有产品需求文档，无设计，有参考 App 如网易一元购，功能与其一样即可。（20 个字符以上）" required="required" style="overflow: hidden; word-wrap: break-word; height: 126px;"></textarea>
                             </div>

                          </div>
                      </div>
                      

                      
                      <div class="form-row">
                          <div class="btn-container-1rScq">
                              <button type="submit" id="send" class="button-c8bJV disabled-DY0Tw primary-3OoVI">完成提交</button>
                          </div>
                      </div>
                      
                    </div>
                    
                  </div>
                </form>
			</div> <!-- /.col-md-8 左侧需求表格 -->



		</div> <!-- /.row -->
	</div> <!-- /.container -->	


	<footer class="site-footer">
		<div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="footer_title">
                        长城信息
                    </div>
                    <ul class="footer_list">
                        <li><a style="color: #777777;" href="<?php echo url('index'); ?>">首页</a></li>
                        <li><a style="color: #777777;" href="<?php echo url('project'); ?>">案例</a></li>
                        <li><a style="color: #777777;" href="<?php echo url('blog'); ?>">博客</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <ul class="footer_list">
                       <li><a href="<?php echo url('publish'); ?>" class="main-button accent-color" style="background-color: #777777;">提交需求<i class="icon-button fa fa-arrow-right"></i></a></li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <div class="footer_title">
                        联系我们
                    </div>
                    <ul class="footer_list">
                        <li>电话：<?php echo config('web_site_tel'); ?></li>
                        <li>传真：<?php echo config('web_site_fax'); ?></li>
                        <li>地址：<?php echo config('web_site_address'); ?></li>
                    </ul>
                </div>
            </div>
			<div class="row">
				<div class="col-md-12">
					<p class="copyright-text"><?php echo config('web_site_copyright'); ?> <br>
<?php echo config('web_site_icp'); ?></p>
				</div> <!-- /.col-md-12 -->
			</div> <!-- /.row -->
		</div> <!-- /.container -->
	</footer> <!-- /.site-footer -->

	<!-- Scripts -->
	<script src="__STATIC__/index/js/min/plugins.min.js"></script>
	<script src="__STATIC__/index/js/min/medigo-custom.min.js"></script>
<!--页面js-->

<script>

$(function(){
  $('input').click(function(){
  	$('input').removeClass("id-botton-2");
  	$('textarea').removeClass("id-botton-2");
	  $(this).addClass("id-botton-2");
  });
  $('textarea').click(function(){
  	$('input').removeClass("id-botton-2");
  	$('textarea').removeClass("id-botton-2");
	  $(this).addClass("id-botton-2");
  });

	$("#name input").change(function(){
		$('div').remove('#name .error-vXvqJ');
		if($(this).val().length<2){
			$('#name').append('<div class="error-vXvqJ"><i class="fa fa-times-circle"></i>请填写正确的姓名，例如：林先生</div>');
		}else{
			$('div').remove('#name .error-vXvqJ');
		}
	})
	$("input[name='tel']").change(function(){
		$('div').remove('#tel .error-vXvqJ');
		if($(this).val().length<11){
			$('#tel').append('<div class="error-vXvqJ"><i class="fa fa-times-circle"></i>请填写正确的联系电话，例如：059522176366</div>');
		}else{
			$('div').remove('#tel .error-vXvqJ');
		}
	})
	$("input[name='qq']").change(function(){
		$('div').remove('#qq .error-vXvqJ');
		if($(this).val().length<5){
			$('#qq').append('<div class="error-vXvqJ"><i class="fa fa-times-circle"></i>请填写正确的QQ/微信号，例如：330362495</div>');
		}else{
			$('div').remove('#qq .error-vXvqJ');
		}
	})
	$("textarea").change(function(){
		$('div').remove('#demand .error-vXvqJ');
		if($(this).val().length<20){
			$('#demand').append('<div class="error-vXvqJ"><i class="fa fa-times-circle"></i>请填写20字以上的需求描述</div>');
		}else{
			$('div').remove('#demand .error-vXvqJ');
		}
	})
	$('#send').click(function(){
		if($('.error-vXvqJ').length>0) return false;
	})


})


</script>


</body>
</html>
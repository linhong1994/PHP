<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:59:"E:\WWW\DolphinPHP/application/index\view\index\project.html";i:1506743391;s:52:"E:\WWW\DolphinPHP/application/index\view\layout.html";i:1506743479;}*/ ?>
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


  
	

	<div class="first-widget parallax" id="portfolioId">
		<div class="parallax-overlay">
			<div class="container pageTitle">
				<div class="row">
					<div class="col-md-12 col-sm-12 text-center" style="margin-bottom:20px;">
						<h2 class="page-title">案例介绍</h2>
					</div> <!-- /.col-md-6 -->
					<div class="col-md-12 col-sm-12 text-center">
						<span class="page-location">为您的需求提供快速可靠的技术解决方案</span>
					</div> <!-- /.col-md-6 -->
				</div> <!-- /.row -->
			</div> <!-- /.container -->
		</div> <!-- /.parallax-overlay -->
	</div> <!-- /.pageTitle -->

	<div class="container">	
		<div class="row project-single">
			<div class="col-md-7">
				<div class="project-img">
					<img src="__STATIC__/index/images/includes/pic-1.jpg" alt="Project Image 1">
				</div> <!-- /.project-img -->
			</div> <!-- /.col-md-8 -->
			<div class="col-md-5">
				<div class="project-info">
					<span class="subtitle">案例一：</span>
					<h3 class="project-title">福建大自然休闲足疗会所</h3>
					<p class="description">福建大自然休闲足疗会所是泉州市最大的休闲娱乐养生会所，致力于为顾客提供最舒适、体贴的健康养生服务。
随着业务扩张和互联网+时代的来临，大自然决定将为顾客提供更加方便快捷，信息化的线上服务作为线下服务的有力补充。</p>
					<p>传统会所+互联网 ，打造新的服务升级</p>
                    <p><a href="<?php echo url('project_image'); ?>#xm1">查看详细...</a></p>
				</div> <!-- /.project-info -->
			</div> <!-- /.col-md-4 -->
		</div> <!-- /.row -->
        
        <div class="row project-single">
			<div class="col-md-7">
				<div class="project-img">
					<img src="__STATIC__/index/images/includes/pic-2.jpg" alt="Project Image 1">
				</div> <!-- /.project-img -->
			</div> <!-- /.col-md-8 -->
			<div class="col-md-5">
				<div class="project-info">
					<span class="subtitle">案例二：</span>
					<h3 class="project-title">宝乐居母婴</h3>
					<p class="description">宝乐居母婴是一家致力于为婴儿及孕妈妈提供优质商品的公司。意识到数据化时期，互联网服务的重要性，宝乐居决定将线下的业务延伸到线上。宝乐居自身没有产品技术团队，他找到了我们来帮助宝乐居打造线上的第一版产品。</p>
					<p>为宝乐居提供产品解决方案</p>
                    <p><a href="<?php echo url('project_image'); ?>#xm2">查看详细...</a></p>
				</div> <!-- /.project-info -->
			</div> <!-- /.col-md-4 -->
		</div> <!-- /.row -->
        
        <div class="row project-single">
			<div class="col-md-7">
				<div class="project-img">
					<img src="__STATIC__/index/images/includes/pic-3.jpg" alt="Project Image 1">
				</div> <!-- /.project-img -->
			</div> <!-- /.col-md-8 -->
			<div class="col-md-5">
				<div class="project-info">
					<span class="subtitle">案例三：</span>
					<h3 class="project-title">泉州市城东中学</h3>
					<p class="description">泉州市城东中学创办于1969年。建校以来，教学设施不断完善，教育质量逐年提高，在泉州中心城区享有较高
声誉。现为福建省一级达标高中。</p>
					<p>结合学校特点，提供一系列解决方案</p>
                    <p><a href="<?php echo url('project_image'); ?>#xm3">查看详细...</a></p>
				</div> <!-- /.project-info -->
			</div> <!-- /.col-md-4 -->
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


</body>
</html>
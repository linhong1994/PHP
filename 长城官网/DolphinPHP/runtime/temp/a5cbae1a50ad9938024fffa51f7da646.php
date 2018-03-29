<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:65:"E:\WWW\DolphinPHP/application/index\view\index\project_image.html";i:1506647505;s:52:"E:\WWW\DolphinPHP/application/index\view\layout.html";i:1506587897;}*/ ?>
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

	<div id="xm1" class="container">	
		<div class="row project-single">
			<div class="col-md-8">
				<div class="project-img">
					<img src="__STATIC__/index/images/includes/pic-1.jpg" alt="Project Image 1">
				</div> <!-- /.project-img -->
			</div> <!-- /.col-md-8 -->
			<div class="col-md-12">
				<div class="project-info">
					<span class="subtitle">案例一：</span>
					<h3 class="project-title">福建大自然休闲足疗会所</h3>
					<p class="description">福建大自然休闲足疗会所是泉州市最大的休闲娱乐养生会所，致力于为顾客提供最舒适、体贴的健康养生服务。
随着业务扩张和互联网+时代的来临，大自然决定将为顾客提供更加方便快捷，信息化的线上服务作为线下服务的有力补充。</p>
					<p>传统会所+互联网 ，打造新的服务升级</p>
<p>大自然团队见到我们，提出的初步需求是在两个月内设计并开发出一款基于微信的web应用，目的是让会员用户可以在线上自助完成会员卡的充值、服务项目的预约、服务项目的评价等功能。</p>
<p>
	<div class="project-img">
		<img src="__STATIC__/index/images/includes/pic-101.jpg" alt="">
	</div> <!-- /.project-img -->
</p>
<p>1.增强客户粘合度       2.数据化运营服务     3.服务直达顾客</p>
<p>1.未开发设计之前，大自然已经申请了微信公众号，也有相对应的运营人员负责图文消息的推送，但功能仅限于
此。开发出web应用，目的就是为了增加顾客的服务体验，让顾客可以服务自动化、消费信息透明化、资金记录
信息化，增加顾客的粘合度。</p>
<p>2.系统包含了会员管理、财务管理、钟房管理等功能模块，让繁杂的管理信息化、数据化。</p>
<p>3.通过服务记录，可以对顾客进行差异化跟踪与服务。</p>
<p>图片设计:持续升级的大自然会员服务系统。</p>
<p>目前为大自然提供的该系统，深度结合了大自然的业务流程，大自然模式正式进入互联网1.0版本的数字化服务。</p>
<p>办公时代:在整体产品策划方案中，我们为大自然设计的是一套可跟随大自然成长的可拓展性系统。若持续迭代快速升级，</p>
<p>我们将会帮助大自然进一步实现"数据化分析"，这套系统，会在顾客消费、服务升级、数据分析等都方面拥有更多出色表现，为大自然创造新的局面。</p>
				</div> <!-- /.project-info -->
			</div> <!-- /.col-md-4 -->
		</div> <!-- /.row -->
	</div> <!-- /.container -->	

	<div class="static-info-project" style="padding:30px 0;">
	</div> <!-- /.static-info-project -->

    <div id="xm2" class="container">	
		<div class="row project-single">
			<div class="col-md-8">
				<div class="project-img">
					<img src="__STATIC__/index/images/includes/pic-2.jpg" alt="Project Image 1">
				</div> <!-- /.project-img -->
			</div> <!-- /.col-md-8 -->
			<div class="col-md-12">
				<div class="project-info">
					<span class="subtitle">案例二：</span>
					<h3 class="project-title">宝乐居母婴</h3>
					<p class="description">宝乐居母婴是一家致力于为婴儿及孕妈妈提供优质商品的公司。意识到数据化时期，互联网服务的重要性，宝乐居决定将线下的业务延伸到线上。宝乐居自身没有产品技术团队，他找到了我们来帮助宝乐居打造线上的第一版产品。</p>
					<p>为宝乐居提供产品解决方案</p>
<p>需求梳理，产品定位</p>
<p>鉴于为第一版本产品，我们不建议宝乐居做成APP，而是做基于微信端定制化的web应用服务。对于宝乐居来说第一款产品需要快速的实现以检验需求及模式，在功能所能达到的效果与能力上app和微信几乎无区别，但App开发周期较长，获取用户成本高，迭代周期长，会耗费更多的人力成本和时间成本，微信端开发是最低成本的快速试错。</p>
<p>数据当道，差异化服务</p>
<p>母婴商城数量日渐增多，如何才能在众多品牌中脱引而出。我们给出的建议是，数据化运营，差异化服务。对于越来越多的客户，定制化服务最能捕获客户的忠诚。因此，我们设计之初，变针对性的增加会员客户的服务功能。</p>
<p>母婴商城的互联网+</p>
<p>母婴商城项目，共历时40多个工作日，通过高效的沟通方式、标准化的项目管理流程，保证了项目按时按质开发完成。</p>
				</div> <!-- /.project-info -->
			</div> <!-- /.col-md-4 -->
		</div> <!-- /.row -->
	</div> <!-- /.container -->	
    
    <div class="static-info-project" style="padding:30px 0;">
	</div> <!-- /.static-info-project -->
    
    <div id="xm3" class="container">	
		<div class="row project-single">
			<div class="col-md-8">
				<div class="project-img">
					<img src="__STATIC__/index/images/includes/pic-3.jpg" alt="Project Image 1">
				</div> <!-- /.project-img -->
			</div> <!-- /.col-md-8 -->
			<div class="col-md-12">
				<div class="project-info">
					<span class="subtitle">案例三：</span>
					<h3 class="project-title">泉州市城东中学</h3>
					<p class="description">泉州市城东中学创办于1969年。建校以来，教学设施不断完善，教育质量逐年提高，在泉州中心城区享有较高
声誉。现为福建省一级达标高中。</p>
					<p>结合学校特点，提供一系列解决方案</p>
          <p>
          	<div class="project-img">
							<img src="__STATIC__/index/images/includes/pic-102.jpg" alt="">
						</div> <!-- /.project-img -->
          </p>
<p>1．简洁美观         2.工作效率提高          3.信息化记录</p>
<p>数字化办公及信息管理</p>
<p>教师办公信息化</p>
<p>学校教职工总数达到200多人，每日所产生的教学信息、办公信息高达数千条，信息的快速更迭需要系统化运作。为此，我们提供一套可供教师链接使用的教学资源内部网，供教职工随时查阅自己的教学资源。</p>
<p>系统让管理流程分配更全面</p>
<p>我们将城东中学的工作业务流程线上化过程中，设计了多个管理权限及流程角色的划分。让每个阶段都有专员进行操作。各层级执行人员的权限及角色划分，保障了教学资源的合理分配，教学的执行质量，任何细节都落实到人。</p>
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
                       <li><a href="<?php echo url('publish'); ?>" class="main-button accent-color">提交需求<i class="icon-button fa fa-arrow-right"></i></a></li>
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
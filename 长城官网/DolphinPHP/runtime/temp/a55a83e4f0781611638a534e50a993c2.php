<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:57:"E:\WWW\DolphinPHP/application/index\view\index\index.html";i:1506743715;s:52:"E:\WWW\DolphinPHP/application/index\view\layout.html";i:1506743479;}*/ ?>
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


  
	

	<section id="homeIntro" class="parallax first-widget">
	    <div class="parallax-overlay">
		    <div class="container home-intro-content">
		        <div class="row">
		        	<div class="col-md-12">
		        		<h2>让技术大牛为你开发，实现你的互联网+</h2>
		        		<p>基于云技术的软件外包服务平台。服务范围：微信、H5、APP、产品策划及开发<br>提供您所需的全套软件开发服务</p>
		        		<a href="<?php echo url('publish'); ?>" class="large-button white-color">提交您的需求 <i class="icon-button fa fa-download"></i></a>
		        	</div> <!-- /.col-md-12 -->
		        </div> <!-- /.row -->
		    </div> <!-- /.container -->
	    </div> <!-- /.parallax-overlay -->
	</section> <!-- /#homeIntro -->

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-header" style="padding-bottom:30px; padding-top:30px;">
                    <h2 class="section-title">我们能帮您</h2>
                    <p class="section-desc">帮助企业互联网化，扩张业务，提升竞争力</p>
                </div> <!-- /.section-header -->
            </div> <!-- /.col-md-12 -->
        </div> <!-- /.row -->
    </div> <!-- /.container -->
	
	<section class="light-content services" style="margin-top:0;">
		<div class="container">
			<div class="row">

				<div class="col-md-4 col-sm-4">
					<div class="service-box-wrap">
						<div class="service-icon-wrap">
							<i class="fa fa-umbrella fa-2x"></i>
						</div> <!-- /.service-icon-wrap -->
						<div class="service-cnt-wrap">
							<h3 class="service-title">传统行业的线上拓展</h3>
							<p>线上拓展并非简单的增加线上渠道或产品，而是充分结合现有业务流程，与互联网技术进行柔和，实现"+互联网"，帮助传统行业实现数字化运营，同时利用大数据、云服务等IT技术推动发展。针对非技术团队的传统行业，长城人将深入了解行业的流程业务，挖掘出可实现的技术需求，帮助企业策划和完善线上业务，为每一个企业打造独立的可持续的解决方案。</p>
						</div> <!-- /.service-cnt-wrap -->
					</div> <!-- /.service-box-wrap -->
				</div> <!-- /.col-md-4 -->

				<div class="col-md-4 col-sm-4">
					<div class="service-box-wrap">
						<div class="service-icon-wrap">
							<i class="fa fa-mobile-phone fa-2x"></i>
						</div> <!-- /.service-icon-wrap -->
						<div class="service-cnt-wrap">
							<h3 class="service-title">互联网转型升级服务</h3>
							<p>结合互联网新型技术，为企业实现转型升级。通过与需求方的不断沟通，帮助企业梳理或优化现有服务流程，定制化设计开发运营管理工具，提升企业对外形象的同时，亦可提高企业的工作和管理效率，助力企业实现互联网的优化升级。</p>
						</div> <!-- /.service-cnt-wrap -->
					</div> <!-- /.service-box-wrap -->
				</div> <!-- /.col-md-4 -->

				<div class="col-md-4 col-sm-4">
					<div class="service-box-wrap">
						<div class="service-icon-wrap">
							<i class="fa fa-pencil-square-o fa-2x"></i>
						</div> <!-- /.service-icon-wrap -->
						<div class="service-cnt-wrap">
							<h3 class="service-title">新产品业务的实现</h3>
							<p>项目的从0到1是建立在新业务的充分认知上，将从最初的需求探索，方案优化，以及中间过程中的体验设计、敏捷开发，直至部署验收、上线。产品生命周期内的服务，长城人都能为您提供到。</p>
						</div> <!-- /.service-cnt-wrap -->
					</div> <!-- /.service-box-wrap -->
				</div> <!-- /.col-md-4 -->

			</div><!-- /.row -->
				
		</div> <!-- /.container -->
	</section> <!-- /.services -->

	

	<section class="testimonials-widget" style="padding:30px 0;">
    
	</section> <!-- /.testimonials-widget -->

	<section class="light-content our-team" style="margin-top:0;">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="section-header" style="padding:30px 0;">
						<h2 class="section-title">我们能做到</h2>
						<p class="section-desc">专业、快速、保质完成您的产品</p>
					</div> <!-- /.section-header -->
				</div> <!-- /.col-md-12 -->
			</div> <!-- /.row -->
		</div> <!-- /.container -->
		<div class="team-members">
			<div class="container">
				<div class="row">

					<div class="col-md-4 col-sm-4" style="margin-bottom:80px;">
						<div class="team-member">
							<div class="thumb-post">
								<img src="__STATIC__/index/images/includes/portfolio.jpg" alt="">
							</div>
							<div class="member-content">
								<h4 class="member-name">定制研发</h4>
								<p>从0撰写第一行代码，100%实现高度定制化研发，我们擅长快速搭建稳定的、可持续更新的高质量互联网产品。</p>
							</div>
						</div> <!-- /.team-member -->
					</div> <!-- /.col-md-4 -->

					<div class="col-md-4 col-sm-4">
						<div class="team-member">
							<div class="thumb-post">
								<img src="__STATIC__/index/images/includes/portfolioId.jpg" alt="">
							</div>
							<div class="member-content">
								<h4 class="member-name">H5解决方案</h4>
								<p>长城人多年的技术积累，整合最新行业技术和技术资源，让您在弹指之间便可获得高质量方案。</p>
							</div>
						</div> <!-- /.team-member -->
					</div> <!-- /.col-md-4 -->

					<div class="col-md-4 col-sm-4">
						<div class="team-member">
							<div class="thumb-post">
								<img src="__STATIC__/index/images/includes/projectimage9.jpg" alt="">
							</div>
							<div class="member-content">
								<h4 class="member-name">微信解决方案</h4>
								<p>基于微信公众号、微信小程序等微信开放功能，提供到微信CRM、OA、O2O、垂直电商、政企等解决方案。</p>
							</div>
						</div> <!-- /.team-member -->
					</div> <!-- /.col-md-4 -->

				</div> <!-- /.row -->
			</div> <!-- /.container -->
		</div> <!-- /.team-members -->
	</section> <!-- /.our-team -->
    
    <section class="testimonials-widget" style="padding:30px 0;"></section>
    
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-header" style="padding-bottom:30px; padding-top:30px;">
                    <h2 class="section-title">我们的客户</h2>
                </div> <!-- /.section-header -->
            </div> <!-- /.col-md-12 -->
        </div> <!-- /.row -->
    </div> <!-- /.container -->
    
    <div class="container index-user">
			<div class="row">
                <div class="col-sm-3">
                    <img src="__STATIC__/index/images/includes/xxym_03.jpg" alt="">
                </div>
                <div class="col-sm-3">
                    <img src="__STATIC__/index/images/includes/xxym_04.jpg" alt="">
                </div>
                <div class="col-sm-3">
                    <img src="__STATIC__/index/images/includes/xxym_05.jpg" alt="">
                </div>
                <div class="col-sm-3">
                    <img src="__STATIC__/index/images/includes/xxym_07.jpg" alt="">
                </div>
            </div> <!-- /.row -->
            <div class="row">
                <div class="col-sm-3">
                    <img src="__STATIC__/index/images/includes/xxym_09.jpg" alt="">
                </div>
                <div class="col-sm-3">
                    <img src="__STATIC__/index/images/includes/xxym_11.jpg" alt="">
                </div>
                <div class="col-sm-3">
                    <img src="__STATIC__/index/images/includes/xxym_12.jpg" alt="">
                </div>
                <div class="col-sm-3">
                    <img src="__STATIC__/index/images/includes/xxym_13.jpg" alt="">
                </div>
            </div> <!-- /.row -->
            <div class="row">
                <div class="col-sm-3">
                    <img src="__STATIC__/index/images/includes/xxym_14.jpg" alt="">
                </div>
                <div class="col-sm-3">
                    <img src="__STATIC__/index/images/includes/xxym_15.jpg" alt="">
                </div>
                <div class="col-sm-3">
                    <img src="__STATIC__/index/images/includes/xxym_16.jpg" alt="">
                </div>
                <div class="col-sm-3">
                    <img src="__STATIC__/index/images/includes/xxym_18.jpg" alt="">
                </div>
            </div> <!-- /.row -->
    </div> <!-- /.container -->
	
	<section id="blogPosts" class="parallax">
	    <div class="parallax-overlay">
		    <div class="container">
		        <div class="row">
		        	<div class="col-md-12">
		        		<div class="section-header">
							<h2 class="section-title">博客新闻</h2>
							<p class="section-desc">一站式软件开发服务平台，我们关注的比您想的还多.</p>
						</div> <!-- /.section-header -->
		        	</div> <!-- /.col-md-12 -->
		        </div> <!-- /.row -->
		        <div class="row latest-posts">
		        	<?php if(is_array($new) || $new instanceof \think\Collection || $new instanceof \think\Paginator): $i = 0; $__LIST__ = $new;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
		        	<div class="col-md-4 col-sm-6">
		        		<div class="blog-post clearfix">
		        			<div class="thumb-post">
		        				<a href="<?php echo url('blog_single',['id'=>$vo['id']]); ?>"><img src="<?php echo get_thumb($vo['pic']); ?>" alt="" class="img-circle"></a>
		        			</div>
		        			<div class="blog-post-content">
		        				<h4 class="post-title"><a href="<?php echo url('blog_single',['id'=>$vo['id']]); ?>"><?php echo $vo['title']; ?></a></h4>
		        				<span class="meta-post-date"><?php echo date("Y/m/d",strtotime($vo['time'])); ?></span>
		        			</div>
		        		</div> <!-- /.blog-post -->
		        	</div> <!-- /.col-md-4 -->
		        	<?php endforeach; endif; else: echo "" ;endif; ?>
		        </div> <!-- /.row -->
		    </div> <!-- /.container -->
	    </div> <!-- /.parallax-overlay -->
	</section> <!-- /#blogPosts -->


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
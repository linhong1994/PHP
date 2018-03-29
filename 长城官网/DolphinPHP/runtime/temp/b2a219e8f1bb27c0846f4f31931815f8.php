<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:56:"E:\WWW\DolphinPHP/application/index\view\index\blog.html";i:1506673020;s:52:"E:\WWW\DolphinPHP/application/index\view\layout.html";i:1506743479;}*/ ?>
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
						<h2 class="page-title">博客</h2>
					</div> <!-- /.col-md-6 -->
					<div class="col-md-12 col-sm-12 text-center">
						<span class="page-location">为您的需求提供快速可靠的技术解决方案</span>
					</div> <!-- /.col-md-6 -->
				</div> <!-- /.row -->
			</div> <!-- /.container -->
		</div> <!-- /.parallax-overlay -->
	</div> <!-- /.pageTitle -->

	<div class="container">	
		<div class="row">

			<div class="col-md-8 blog-posts">
				<div class="row">
					<div class="col-md-12">
						<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
						<div class="post-blog">
							<div class="blog-image">
								<a href="<?php echo url('blog_single',['id'=>$vo['id']]); ?>">
									<img src="<?php echo get_files_path($vo['pic']); ?>" alt="">
								</a>
							</div> <!-- /.blog-image -->
							<div class="blog-content">
								<span class="meta-date"><?php echo date("Y/m/d",strtotime($vo['time'])); ?></span>
								<span class="meta-comments">阅读数：<?php echo $vo['view']; ?></span>
								<span class="meta-author">作者：<?php echo $vo['author']; ?></span>
								<h3><a href="<?php echo url('blog_single',['id'=>$vo['id']]); ?>"><?php echo $vo['title']; ?></a></h3>
								<p><?php echo $vo['intro']; ?><a href="<?php echo url('blog_single',['id'=>$vo['id']]); ?>">查看详细...</a></p>
							</div> <!-- /.blog-content -->
						</div> <!-- /.post-blog -->
						<?php endforeach; endif; else: echo "" ;endif; ?>					
					</div> <!-- /.col-md-12 -->
					<div class="col-md-12">
						<?php echo $list->render(); ?>

					</div> <!-- /.col-md-12 -->
                    
				</div> <!-- /.row -->
			</div> <!-- /.col-md-8 -->

			<div class="col-md-4">
				<div class="sidebar">
					<div class="sidebar-widget">
						<h5 class="widget-title">热门博客</h5>
						
						<?php if(is_array($hot) || $hot instanceof \think\Collection || $hot instanceof \think\Paginator): $i = 0; $__LIST__ = $hot;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
						<div class="last-post clearfix">
							<div class="thumb pull-left">
								<a href="<?php echo url('blog_single',['id'=>$vo['id']]); ?>"><img src="<?php echo get_thumb($vo['pic']); ?>" alt=""></a>
							</div>
							<div class="content">
								<span><?php echo date("Y/m/d",strtotime($vo['time'])); ?></span>
								<h4><a href="<?php echo url('blog_single',['id'=>$vo['id']]); ?>"><?php echo $vo['title']; ?></a></h4>
							</div>
						</div> <!-- /.last-post -->
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</div> <!-- /.sidebar-widget -->
				</div> <!-- /.sidebar -->
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
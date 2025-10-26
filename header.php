<?php ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> >
<head>
	<meta charset="<?php bloginfo('charset'); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> >
<header class="site-header">
	<div class="header-inner">
		<div class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a></div>
		<div class="header-left">
			<a class="header-link" href="<?php echo esc_url(home_url('/')); ?>">首页</a>
		</div>
		<div class="header-center">
			<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
				<label>
					<input type="search" class="search-field" placeholder="输入关键词回车搜索" value="<?php echo get_search_query(); ?>" name="s" />
				</label>
			</form>
		</div>
		<div class="header-right">
			<button class="theme-toggle" aria-label="切换主题"><span class="toggle-icon"></span></button>
			<a class="header-link" href="<?php echo esc_url(home_url('/%e8%81%94%e7%b3%bb%e6%88%91/')); ?>">联系我</a>
		</div>
	</div>
</header>


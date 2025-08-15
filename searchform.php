<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
	<label>
		<input type="search" class="search-field" placeholder="输入关键词回车搜索" value="<?php echo get_search_query(); ?>" name="s" />
	</label>
</form>

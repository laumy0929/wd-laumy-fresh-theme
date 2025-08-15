(function(){
	function ready(fn){ if(document.readyState!=='loading'){ fn(); } else { document.addEventListener('DOMContentLoaded', fn); } }

	ready(function(){
		// Toggle dark mode
		var toggle = document.querySelector('.theme-toggle');
		if(toggle){
			toggle.addEventListener('click', function(){
				document.body.classList.toggle('dark');
				try { localStorage.setItem('laumy_dark', document.body.classList.contains('dark') ? '1':'0'); } catch(e){}
			});
			try { if(localStorage.getItem('laumy_dark')==='1'){ document.body.classList.add('dark'); } } catch(e){}
		}

		// Categories expand
		document.addEventListener('click', function(e){
			var header = e.target.closest('.category-header');
			if(!header) return;
			var item = header.closest('.category-item');
			if(!item) return;
			if(header.querySelector('a') && e.target.closest('a')){ return; }
			item.classList.toggle('expanded');
		});

		// TOC fold/unfold on parent title click if has children
		document.addEventListener('click', function(e){
			var link = e.target.closest('.toc-item > .toc-link');
			if(!link) return;
			var li = link.parentElement;
			var children = li.querySelector(':scope > .toc-children');
			if(children){
				e.preventDefault();
				children.style.display = children.style.display === 'none' ? '' : 'none';
			}
		});
	});
})();

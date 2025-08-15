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

		// ===== TOC collapse + scroll spy =====
		(function initTOC(){
			var tocRoot = document.querySelector('.left-sidebar');
			if(!tocRoot) return;

			// add arrows and collapse ability
			var tocItems = tocRoot.querySelectorAll('.toc-item');
			tocItems.forEach(function(li){
				var children = li.querySelector(':scope > .toc-children');
				var link = li.querySelector(':scope > .toc-link');
				if(children && link){
					li.classList.add('has-children','expanded');
					var arrow = document.createElement('span');
					arrow.className = 'toc-arrow';
					arrow.setAttribute('aria-hidden','true');
					arrow.textContent = '▸';
					li.insertBefore(arrow, link);
					arrow.addEventListener('click', function(ev){
						ev.preventDefault(); ev.stopPropagation();
						li.classList.toggle('expanded');
					});
				}
			});

			// Scroll spy highlight
			var headings = Array.prototype.slice.call(document.querySelectorAll('h2[id^="sec-"], h3[id^="sec-"], h4[id^="sec-"], h5[id^="sec-"]'));
			if(headings.length===0) return;

			function setActive(id){
				tocRoot.querySelectorAll('.toc-link.active').forEach(function(a){ a.classList.remove('active'); });
				var active = tocRoot.querySelector('.toc-link[href="#'+CSS.escape(id)+'"]');
				if(active){
					active.classList.add('active');
					// expand all ancestors
					var li = active.closest('.toc-item');
					while(li){
						li.classList.add('expanded');
						li = li.parentElement && li.parentElement.closest('.toc-item');
					}
					// ensure visibility
					var rect = active.getBoundingClientRect();
					var cont = tocRoot.getBoundingClientRect();
					if(rect.top < cont.top || rect.bottom > cont.bottom){
						active.scrollIntoView({block:'nearest'});
					}
				}
			}

			var lastId = null;
			function onScroll(){
				var pos = window.pageYOffset || document.documentElement.scrollTop;
				var headerOffset = 80; // compensate fixed header
				var current = null;
				for(var i=0;i<headings.length;i++){
					if(headings[i].getBoundingClientRect().top <= headerOffset){
						current = headings[i];
					}else{ break; }
				}
				if(current && current.id !== lastId){
					lastId = current.id;
					setActive(lastId);
				}
			}
			window.addEventListener('scroll', onScroll, {passive:true});
			onScroll();
		})();
	});
})();

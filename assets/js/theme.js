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

		// Categories expand/collapse with state persistence
		var categoryStates = {};
		var categoryClickHandler;
		
		// 保存分类展开状态
		function saveCategoryStates() {
			var states = {};
			document.querySelectorAll('.category-item').forEach(function(item) {
				var categoryName = item.querySelector('.category-link').textContent.trim();
				states[categoryName] = item.classList.contains('expanded');
			});
			try { localStorage.setItem('laumy_category_states', JSON.stringify(states)); } catch(e){}
		}
		
		// 更新箭头方向
		function updateArrow(item, expanded) {
			var arrow = item.querySelector('.category-arrow');
			if(arrow) {
				arrow.textContent = expanded ? '▼' : '▶';
			}
		}
		
		// 分类点击事件
		categoryClickHandler = function(e){
			var header = e.target.closest('.category-header');
			if(!header || e.target.closest('a')) return;
			
			var item = header.closest('.category-item');
			if(!item) return;
			
			var expanded = item.classList.toggle('expanded');
			updateArrow(item, expanded);
			saveCategoryStates();
		};
		
		document.addEventListener('click', categoryClickHandler);
		
		// 页面加载时恢复状态
		try {
			var states = JSON.parse(localStorage.getItem('laumy_category_states') || '{}');
			document.querySelectorAll('.category-item').forEach(function(item) {
				var categoryName = item.querySelector('.category-link').textContent.trim();
				if (states[categoryName]) {
					item.classList.add('expanded');
					updateArrow(item, true);
				}
			});
		} catch(e) {}

		// ===== TOC collapse + scroll spy =====
		(function initTOC(){
			var tocRoot = document.querySelector('.left-sidebar');
			if(!tocRoot) return;

			var tocArrows = [];
			var scrollHandler;
			
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
					
					var arrowClickHandler = function(ev){
						ev.preventDefault(); ev.stopPropagation();
						li.classList.toggle('expanded');
					};
					
					arrow.addEventListener('click', arrowClickHandler);
					tocArrows.push({element: arrow, handler: arrowClickHandler});
					
					li.insertBefore(arrow, link);
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
			scrollHandler = function(){
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
			};
			
			window.addEventListener('scroll', scrollHandler, {passive:true});
			scrollHandler();
			
			// 清理函数
			return function cleanup() {
				// 移除TOC箭头事件监听器
				tocArrows.forEach(function(arrow) {
					arrow.element.removeEventListener('click', arrow.handler);
				});
				// 移除滚动事件监听器
				window.removeEventListener('scroll', scrollHandler, {passive:true});
			};
		})();
		
		// 页面卸载时清理事件监听器
		window.addEventListener('beforeunload', function() {
			// 移除分类点击事件
			document.removeEventListener('click', categoryClickHandler);
		});
	});
})();

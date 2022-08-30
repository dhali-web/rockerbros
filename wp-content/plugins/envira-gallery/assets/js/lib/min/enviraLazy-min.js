var enviraLazy=function(){var e=!1,t=null,n=null,i=!1,r=!1,a="undefined"!=typeof IntersectionObserver,l=function(e){if(null===t&&(t=window.innerWidth||document.documentElement.clientWidth||document.body.clientWidth,n=window.innerHeight||document.documentElement.clientHeight||document.body.clientHeight,null===t))return!1;var i=e.getBoundingClientRect(),r=i.top,a=i.left,l=i.width,o=i.height,d=n>r&&r+o>0&&t>a&&a+l>0;return d};jQuery.fn.exists=function(){return this.length>0};var o=function(t,n){var i=n.getAttribute("data-envira-srcset");if(null!==i)if(i=i.trim(),i.length>0){i=i.split(",");for(var r=[],a=i.length,l=0;a>l;l++){var o=i[l].trim();if(0!==o.length){var d=o.lastIndexOf(" ");if(-1===d)var u=o,s=999998;else var u=o.substr(0,d),s=parseInt(o.substr(d+1,o.length-d-2),10);var c=!1;-1!==u.indexOf(".webp",u.length-5)?e&&(c=!0):c=!0,c&&r.push([u,s])}}r.sort(function(e,t){if(e[1]<t[1])return-1;if(e[1]>t[1])return 1;if(e[1]===t[1]){if(-1!==t[0].indexOf(".webp",t[0].length-5))return 1;if(-1!==e[0].indexOf(".webp",e[0].length-5))return-1}return 0}),i=r}else i=[];else i=[];for(var f=t.offsetWidth*window.devicePixelRatio,v=null,a=i.length,l=0;a>l;l++){var y=i[l];if(y[1]>=f){v=y;break}}if(null===v&&(v=[n.getAttribute("data-envira-src"),999999]),"undefined"==typeof t.lastSetOption&&(t.lastSetOption=["",0]),t.lastSetOption[1]<v[1]){var m=0===t.lastSetOption[1],g=v[0],w=new Image;w.addEventListener("load",function(){if(n.setAttribute("srcset",g),n.setAttribute("src",g),m){var e=t.getAttribute("data-onlazyload");null!==e&&new Function(e).bind(t)()}},!1),w.addEventListener("error",function(){t.lastSetOption=["",0]},!1),w.onload=function(){if("envira-lazy"==t.getAttribute("class")&&$(t).not("img"))var e=t.firstElementChild,n=t,i=e.id,r=e.src,a=jQuery(e).data("envira-gallery-id"),l=jQuery(t).data("envira-item-id"),o=this.naturalWidth,d=this.naturalHeight;else var e=w,n=t,i=t.id,r=t.src,a=jQuery(t).data("envira-gallery-id"),l=jQuery(t).data("envira-item-id"),o=this.naturalWidth,d=this.naturalHeight;(void 0===a||null===a)&&(a=0),jQuery(document).trigger({type:"envira_image_lazy_load_complete",container:n,image_src:r,image_id:i,item_id:l,gallery_id:a,naturalWidth:o,naturalHeight:d})},w.onerror=function(){console.error("Cannot load image")},w.src=g,t.lastSetOption=v}},d=function(){t=window.innerWidth||document.documentElement.clientWidth||document.body.clientWidth,n=window.innerHeight||document.documentElement.clientHeight||document.body.clientHeight},u=function(e){r=e,alert("setting - "+r)},s=function(e){if("undefined"!=typeof e){var t=function(e,t){for(var n=e.length,i=0;n>i;i++){var r=e[i],a=t?r:r.parentNode;l(a)===!0&&o(a,r)}};if(e){if("string"!=typeof e)return;("undefined"===envira_gallery.ll_delay||envira_gallery.ll_initial===!1||"undefined"===envira_gallery.ll_initial)&&(envira_gallery.ll_delay=0);var n=setTimeout(function(){jQuery(e+" div.envira-lazy > img").exists()?t(document.querySelectorAll(e+" div.envira-lazy > img"),!1):jQuery(e+" img.envira-lazy").exists()&&t(document.querySelectorAll(e+" img.envira-lazy"),!0),1==envira_gallery.ll_initial},envira_gallery.ll_delay)}}};if("srcset"in document.createElement("img")&&"undefined"!=typeof window.devicePixelRatio&&"undefined"!=typeof window.addEventListener&&"undefined"!=typeof document.querySelectorAll){d();var c=new Image;c.src="data:image/webp;base64,UklGRiQAAABXRUJQVlA4IBgAAAAwAQCdASoCAAEADMDOJaQAA3AA/uuuAAA=",c.onload=c.onerror=function(){if(e=2===c.width,a){var t=function(){for(var e=document.querySelectorAll(".envira-lazy"),t=e.length,i=0;t>i;i++){var r=e[i];"undefined"==typeof r.responsivelyLazyObserverAttached&&(r.responsivelyLazyObserverAttached=!0,n.observe(r))}},n=new IntersectionObserver(function(e){for(var t in e){var n=e[t];if(n.intersectionRatio>0){var i=n.target;if("img"!==i.tagName.toLowerCase()){var r=i.querySelector("img");null!==r&&o(i,r)}else o(i,i)}}});s()}else var i=window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||function(e){window.setTimeout(e,1e3/60)},r=!0,l=function(){r&&(r=!1),i.call(null,l)},u=function(){r=!0,l()},f=function(){for(var e=document.querySelectorAll(".envira-lazy"),t=e.length,n=0;t>n;n++)for(var i=e[n].parentNode;i&&"html"!==i.tagName.toLowerCase();)"undefined"==typeof i.responsivelyLazyScrollAttached&&(i.responsivelyLazyScrollAttached=!0,i.addEventListener("scroll",u)),i=i.parentNode};var v=function(){if(a)var e=null;if(window.addEventListener("resize",function(){d(),a?(window.clearTimeout(e),e=window.setTimeout(function(){s()},300)):u()}),a?(window.addEventListener("load",s),t()):(window.addEventListener("scroll",u),window.addEventListener("load",u),f()),"undefined"!=typeof MutationObserver){var n=new MutationObserver(function(){a?(t(),s()):(f(),u())});n.observe(document.querySelector("body"),{childList:!0,subtree:!0})}};"loading"===document.readyState?document.addEventListener("DOMContentLoaded",v):v()}}return{run:s,isVisible:l,setGalleryClass:u}}();window.enviraLazy=enviraLazy,module.exports=enviraLazy;
(function(a,b){typeof exports==='object'&&typeof module!=='undefined'?b(exports):typeof define==='function'&&define.amd?define(['exports'],b):(a=a||self,b(a.cy={}));}(this,function(a){'use strict';var b=typeof window!=='undefined'&&window.flatpickr!==undefined?window.flatpickr:{l10ns:{}};var c={weekdays:{shorthand:['Sul','Llun','Maw','Mer','Iau','Gwe','Sad'],longhand:['Dydd Sul','Dydd Llun','Dydd Mawrth','Dydd Mercher','Dydd Iau','Dydd Gwener','Dydd Sadwrn']},months:{shorthand:['Ion','Chwef','Maw','Ebr','Mai','Meh','Gorff','Awst','Medi','Hyd','Tach','Rhag'],longhand:['Ionawr','Chwefror','Mawrth','Ebrill','Mai','Mehefin','Gorffennaf','Awst','Medi','Hydref','Tachwedd','Rhagfyr']},firstDayOfWeek:1,ordinal:function(a){return a===1?'af':a===2?'ail':a===3||a===4?'ydd':a===5||a===6?'ed':a>=7&&a<=10||a==12||a==15||a==18||a==20?'fed':a==11||a==13||a==14||a==16||a==17||a==19?'eg':a>=21&&a<=39?'ain':'';},time_24hr:!0};b.l10ns.cy=c;var d=b.l10ns;a.Welsh=c,a.default=d,Object.defineProperty(a,'__esModule',{value:!0});}));
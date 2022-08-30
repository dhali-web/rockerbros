// ==========================================================================
//
// SlideShow
// Enables slideshow functionality
//
// Example of usage:
// $.envirabox.getInstance().SlideShow.start()
//
// ==========================================================================
(function(document, $) {
	'use strict';

	var SlideShow = function(instance) {
		this.instance = instance;
		this.init();
	};

	$.extend(SlideShow.prototype, {
		timer: null,
		isActive: false,
		$button: null,
		speed: 3000,

		init: function() {
			var self = this;

			self.$button = $('[data-envirabox-play]').on('click', function(
				e,
			) {
				e.preventDefault();
				self.toggle();
			});

			if (
				self.instance.group.length < 2 ||
				!self.instance.group[self.instance.currIndex].opts.slideShow
			) {
				self.$button.hide();
			}
		},

		set: function() {
			var self = this;

			// Check if reached last element
			if (
				self.instance &&
				self.instance.current &&
				(self.instance.current.opts.loop ||
					self.instance.currIndex <
						self.instance.group.length - 1)
			) {
				self.timer = setTimeout(function() {
					if (self.isActive == true) {
						self.instance.next();
					}
				}, self.instance.current.opts.slideShow.speed ||
					self.speed);
			} else {
				self.stop();
				self.instance.idleSecondsCounter = 0;
				self.instance.showControls();
			}
		},

		clear: function() {
			var self = this;

			clearTimeout(self.timer);

			self.timer = null;
		},

		start: function() {
			var self = this;
			var current = self.instance.current;

			if (
				self.instance &&
				current &&
				(current.opts.loop ||
					current.index < self.instance.group.length - 1)
			) {
				self.isActive = true;

				self.$button
					.attr(
						'title',
						current.opts.i18n[current.opts.lang].PLAY_STOP,
					)
					.addClass('envirabox-button--pause');
				self.$button.parent().addClass('envirabox-button--pause');
				if (current.isComplete) {
					self.set();
				}
			}
		},

		stop: function() {
			var self = this;
			var current = self.instance.current;

			self.clear();

			self.$button
				.attr(
					'title',
					current.opts.i18n[current.opts.lang].PLAY_START,
				)
				.removeClass('envirabox-button--pause');
			self.$button.parent().removeClass('envirabox-button--pause');

			self.isActive = false;
		},

		toggle: function() {
			var self = this;

			if (self.isActive) {
				self.stop();
			} else {
				self.start();
			}
		},
	});

	$(document).on({
		'onInit.eb': function(e, instance) {
			if (instance && !instance.SlideShow) {
				instance.SlideShow = new SlideShow(instance);
			}
		},

		'beforeShow.eb': function(e, instance, current, firstRun) {
			var SlideShow = instance && instance.SlideShow;

			if (firstRun) {
				if (SlideShow && current.opts.slideShow.autoStart) {
					SlideShow.start();
				}
			} else if (SlideShow && SlideShow.isActive) {
				SlideShow.clear();
			}
		},

		'afterShow.eb': function(e, instance, current) {
			var SlideShow = instance && instance.SlideShow;

			if (SlideShow && SlideShow.isActive) {
				SlideShow.set();
			}
		},

		'afterKeydown.eb': function(e, instance, current, keypress, keycode) {
			var SlideShow = instance && instance.SlideShow;

			// "P" or Spacebar
			if (
				SlideShow &&
				current.opts.slideShow &&
				(keycode === 80 || keycode === 32) &&
				!$(document.activeElement).is('button,a,input')
			) {
				keypress.preventDefault();

				SlideShow.toggle();
			}
		},

		'beforeClose.eb onDeactivate.eb': function(e, instance) {
			var SlideShow = instance && instance.SlideShow;

			if (SlideShow) {
				SlideShow.stop();
			}
		},
	});

	// Page Visibility API to pause slideshow when window is not active
	$(document).on('visibilitychange', function() {
		var instance = $.envirabox.getInstance();
		var SlideShow = instance && instance.SlideShow;

		if (SlideShow && SlideShow.isActive) {
			if (document.hidden) {
				SlideShow.clear();
			} else {
				SlideShow.set();
			}
		}
	});
})(document, window.jQuery);

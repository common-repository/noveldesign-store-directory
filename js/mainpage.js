jQuery(document).ready(function($) {
	var fs = $('.shop_carousel .flexslider'),
		dataItem = fs.data('item'),
		item = fs.find('.item');
	// Wrap divs
	for (var i = 0; i < item.length; i += dataItem) {
		item.slice(i, i + dataItem).wrapAll('<div class="items"></div>');
	}
	/*$('.shop_carousel .flexslider').flexslider({
		animation: "slide",
		animationLoop: false,
		itemWidth: 210,
		itemMargin: 5
	});*/
	fs.flexslider({
		selector: '.slides > .items',
		animation: "slide",
		animationLoop: false,
        itemWidth: 210,
		itemMargin: 5
	});
    $('.flexslider.similar-shops').flexslider({
		selector: '.slides > .items',
		animation: "slide",
		animationLoop: false,
        itemWidth: 210,
		itemMargin: 5
	});
});
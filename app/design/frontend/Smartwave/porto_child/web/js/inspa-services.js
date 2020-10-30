    jQuery('.link').click(function() {
    // get the contents of the parent of the link that was clicked
    var linkText = jQuery(this).html()+'<a class="vc_general vc_btn3 vc_btn3-size-lg vc_btn3-shape-square vc_btn3-style-classic vc_btn3-color-turquoise" href="https://inspa.zenoti.com/webstoreNew" title="" target="_blank">BOOK AN APPOINTMENT</a><br><br>';

    // replace the contents of the div with the link text
    jQuery('#details .body').html(linkText).hide().fadeIn(2000);
    jQuery('#details').addClass('active');
});

jQuery('.facials .link').click(function() {
     // change the background image of the details column
    //jQuery('.page-header').removeClass('bg2 bg3 bg4 bg5 bg6 bg7 bg8').addClass('bg1');
    jQuery('#smallpic').removeClass('bg2 bg3 bg4 bg5 bg6 bg7 bg8').addClass('bg1');
     // cancel the default action of the link by returning false
    return false;
});

jQuery('.manicures .link').click(function() {
     // change the background image of the details column
    //jQuery('.page-header').removeClass('bg1 bg3 bg4 bg5 bg6 bg7 bg8').addClass('bg2');
    jQuery('#smallpic').removeClass('bg1 bg3 bg4 bg5 bg6 bg7 bg8').addClass('bg2');
     // cancel the default action of the link by returning false
    return false;
});
jQuery('.pedicures .link').click(function() {
     // change the background image of the details column
    //jQuery('.page-header').removeClass('bg1 bg2 bg4 bg5 bg6 bg7 bg8').addClass('bg3');
    jQuery('#smallpic').removeClass('bg1 bg2 bg4 bg5 bg6 bg7 bg8').addClass('bg3');
     // cancel the default action of the link by returning false
    return false;
});

jQuery('.massage .link').click(function() {
     // change the background image of the details column
    //jQuery('.page-header').removeClass('bg1 bg2 bg3 bg4 bg5 bg6 bg8').addClass('bg7');
    jQuery('#smallpic').removeClass('bg1 bg2 bg3 bg4 bg5 bg6 bg8').addClass('bg7');
     // cancel the default action of the link by returning false
    return false;
});

jQuery('.massage-add-ons .link').click(function() {
     // change the background image of the details column
    //jQuery('.page-header').removeClass('bg1 bg2 bg3 bg5 bg6 bg7 bg8').addClass('bg4');
    jQuery('#smallpic').removeClass('bg1 bg2 bg3 bg5 bg6 bg7 bg8').addClass('bg4');
     // cancel the default action of the link by returning false
    return false;
});

jQuery('.waxing .link').click(function() {
     // change the background image of the details column
    //jQuery('.page-header').removeClass('bg1 bg2 bg3 bg4 bg5 bg6 bg7').addClass('bg8');
    jQuery('#smallpic').removeClass('bg1 bg2 bg3 bg4 bg5 bg6 bg7').addClass('bg8');
     // cancel the default action of the link by returning false
    return false;
});

jQuery('.lashes .link').click(function() {
     // change the background image of the details column
    //jQuery('.page-header').removeClass('bg1 bg2 bg3 bg4 bg5 bg7 bg8').addClass('bg6');
    jQuery('#smallpic').removeClass('bg1 bg2 bg3 bg4 bg5 bg7 bg8').addClass('bg6');
     // cancel the default action of the link by returning false
    return false;
});

jQuery('.princess .link').click(function() {
     // change the background image of the details column
    //jQuery('.page-header').removeClass('bg1 bg2 bg3 bg4 bg6 bg7 bg8').addClass('bg5');
    jQuery('#smallpic').removeClass('bg1 bg2 bg3 bg4 bg6 bg7 bg8').addClass('bg5');
     // cancel the default action of the link by returning false
    return false;
});

jQuery('.nail-add-ons .link').click(function() {
     // change the background image of the details column
    //jQuery('.page-header').removeClass('bg1 bg3 bg4 bg5 bg6 bg7 bg8').addClass('bg2');
    jQuery('#smallpic').removeClass('bg1 bg3 bg4 bg5 bg6 bg7 bg8').addClass('bg2');
     // cancel the default action of the link by returning false
    return false;
});



var Auction = Class.create();Auction.prototype = {    initialize: function (changeProductUrl) {        this.changeProductUrl = changeProductUrl;    },    changeProduct: function (product_id) {        var url = this.changeProductUrl;        if (url.indexOf('?') == -1)            url += '?product_id=' + product_id;        else            url += '&product_id=' + product_id;        new Ajax.Updater('product_name_contain', url, {            method: 'get', onComplete: function () {                updateProductName();            }, onFailure: ""        });    },    checkBidderName: function () {        var bidder_name = $('bidder_name').value;        $('bidder_name_form_button').hide();        var url = this.changeProductUrl;        if (url.indexOf('?') == -1)            url += '?bidder_name=' + bidder_name;        else            url += '&bidder_name=' + bidder_name;        new Ajax.Updater('bidder-notice', url, {            method: 'get', onComplete: function () {                validBidderName();            }, onFailure: ""        });        $('biddername-please-wait').style.display = "block";        $('bidder-notice').style.display = "none";        $('bidder_name').removeClassName('validation-passed');    }}document.observe('dom:loaded', function () {    var allowBuyOut = $$('#auction_producttab_form_product_auction_content #allow_buyout')[0];    if (allowBuyOut) {        if (allowBuyOut.value == '1') {            $$('#day_to_buy')[0].up('tr').style.display = 'none';        }        allowBuyOut.observe('change', function () {            if (allowBuyOut.value == '1') {                $$('#day_to_buy')[0].up('tr').style.display = 'none';            } else {                $$('#day_to_buy')[0].up('tr').style.display = '';            }        });    }	var typeAuction = $$('#auction_producttab_form_product_auction_content #type_auction')[0];	if (typeAuction) {        var config_time_obj = document.getElementById('configtime');        var limit_time_obj = document.getElementById('limit_time');        config_time_obj.style.paddingLeft = '5px';        config_time_obj.style.paddingTop = '5px';        if (typeAuction.value == '1') {            $$('#configtime')[0].up('tr').style.display = 'none';            $$('#limit_time')[0].up('tr').style.display = '';            config_time_obj.classList.remove('required-entry');            limit_time_obj.classList.add('required-entry');        } else {            $$('#configtime')[0].up('tr').style.display = '';            $$('#limit_time')[0].up('tr').style.display = 'none';            config_time_obj.classList.add('required-entry');            limit_time_obj.classList.remove('required-entry');        }        typeAuction.observe('change', function () {            if (typeAuction.value == '1') {                $$('#configtime')[0].up('tr').style.display = 'none';                $$('#limit_time')[0].up('tr').style.display = '';                config_time_obj.classList.remove('required-entry');                limit_time_obj.classList.add('required-entry');            } else {                $$('#configtime')[0].up('tr').style.display = '';                $$('#limit_time')[0].up('tr').style.display = 'none';                config_time_obj.classList.add('required-entry');                limit_time_obj.classList.remove('required-entry');            }        });    }});function validBidderName() {    $('biddername-please-wait').style.display = "none";    $('bidder-notice').style.display = "block";    if ($('is_valid_bidder_name') != null) {        if ($('is_valid_bidder_name').value == '2') {//invalid            $('bidder_name').removeClassName('validation-passed');        } else {            $('bidder_name_form_button').show();        }    }}var AuctionUpdater = Class.create();AuctionUpdater.prototype = {    initialize: function (url) {        this.url = url;    },    updateInfo: function (elementId, productId) {        var rq_url = this.url;        var auctionId = ($('auction_id') != null) ? $('auction_id').value : null;        if ((auctionId != null ) && elementId == 'auction_info_' + auctionId) {            if ($('is_bidding').value != '1') {                if (rq_url.indexOf('?') == -1)                    rq_url += '?current_bid_id=';                else                    rq_url += '&current_bid_id=';                if ($('current_bid_id_' + auctionId))                    rq_url += $('current_bid_id_' + auctionId).value;                new Ajax.Updater('results_bid_after_' + auctionId, rq_url, {                    method: 'get', onComplete: function () {                        updateAuctionComplete();                    }, onFailure: ""                });            }        } else {            if (rq_url.indexOf('?') == -1)                rq_url += '?current_bid_id=';            else                rq_url += '&current_bid_id=';            if ($('current_bid_id_' + productId) != null)                rq_url += $('current_bid_id_' + productId).value;            new Ajax.Updater('results_update_auction_' + productId, rq_url, {                method: 'get', onComplete: function () {                    updateAuctionListComplete();                }, onFailure: ""            });        }    }}var AuctionTimeCounter = Class.create();AuctionTimeCounter.prototype = {    //params now_time, end_time : seconds    //changed by Hai.Ta (function(now_time,end_time,auction_id, day_label, days_label, month_label, months_label, year_label, years_label))    //Add @param  day_label, days_label, month_label, months_label, year_label, years_label into function this    initialize: function (now_time, end_time, auction_id, day_label, days_label, month_label, months_label, year_label, years_label) {        this.now_time = parseInt(now_time) * 1000;        this.end_time = parseInt(end_time) * 1000;        this.auction_id = auction_id;        this.end = new Date(this.end_time);        var endDate = this.end;        this.second = endDate.getSeconds();        this.minute = endDate.getMinutes();        this.hour = endDate.getHours();        this.day = endDate.getDate();        this.month = endDate.getMonth();        this.days_label = days_label;        this.months_label = months_label;        this.years_label = years_label;        this.year_label = year_label;        this.month_label = month_label;        this.day_label = day_label;        var yr;        if (endDate.getYear() < 1900)            yr = endDate.getYear() + 1900;        else            yr = endDate.getYear();        this.year = yr;    },    setTimeleft: function (timeleft_id) {        var now = new Date(this.now_time);        var yr;        if (now.getYear() < 1900)            yr = now.getYear() + 1900;        else            yr = now.getYear();        var endtext = '0';        var timerID;        var sec = this.second - now.getSeconds();        var min = this.minute - now.getMinutes();        var hr = this.hour - now.getHours();        var dy = this.day - now.getDate();        var mnth = this.month - now.getMonth();        yr = this.year - yr;        var daysinmnth = 32 - new Date(now.getYear(), now.getMonth(), 32).getDate();        if (sec < 0) {            sec = (sec + 60) % 60;            min--;        }        if (min < 0) {            min = (min + 60) % 60;            hr--;        }        if (hr < 0) {            hr = (hr + 24) % 24;            dy--;        }        if (dy < 0) {            dy = (dy + daysinmnth) % daysinmnth;            mnth--;        }        if (mnth < 0) {            mnth = (mnth + 12) % 12;            yr--;        }        var sectext = "";        var mintext = ":";        var hrtext = ":";        var dytext = this.days_label;        var mnthtext = this.months_label;        var yrtext = this.years_label;        if (yr == 1)            yrtext = this.year_label;        if (mnth == 1)            mnthtext = this.month_label;        if (dy == 1)            dytext = this.day_label;        if (hr == 1)            hrtext = ":";        if (min == 1)            mintext = ":";        if (sec == 1)            sectext = "";        if (dy < 10)            dy = '0' + dy;        if (hr < 10)            hr = '0' + hr;        if (min < 10)            min = '0' + min;        if (sec < 10)            sec = '0' + sec;        if (yr <= 0)            yrtext = ''        else            yrtext = yr + yrtext;        if ((mnth <= 0))            mnthtext = ''        else            mnthtext = '<span class="timeleft-text">' + mnth + '</span>' + mnthtext;        if (dy <= 0 && mnth > 0)            dytext = ''        else            dytext = '<span class="timeleft-text">' + dy + '</span>' + dytext;        if (hr <= 0 && dy > 0)            hrtext = ''        else            hrtext = '<span class="timeleft-text">' + hr + '</span>' + hrtext;        if (min < 0)            mintext = ''        else            mintext = '<span class="timeleft-text">' + min + '</span>' + mintext;        if (sec < 0)            sectext = ''        else            sectext = '<span class="timeleft-text">' + sec + '</span>' + sectext;        if (now >= this.end) {            if(document.getElementById(timeleft_id) != null)                document.getElementById(timeleft_id).innerHTML = endtext;            clearTimeout(timerID);        }        else {			if(document.getElementById(timeleft_id) != null)				document.getElementById(timeleft_id).innerHTML = yrtext + mnthtext + dytext + hrtext + mintext + sectext;        }        if (this.now_time == this.end_time) {            location.reload(true);            return;        }        this.now_time = this.now_time + 1000; //incres 1000 miliseconds        timerID = setTimeout("setAuctionTimeleft(" + (this.now_time / 1000) + "," + (this.end_time / 1000) + ",'" + timeleft_id + "','" + this.auction_id + "','" + this.day_label + "','" + this.days_label + "','" + this.month_label + "','" + this.months_label + "','" + this.year_label + "','" + this.years_label + "');", 1000);    }}function setTimeLeftV2(auction_id){    setTimeout(setUpdateTimerLeftV2(auction_id),1000);}function setUpdateTimerLeftV2(auction_id){    console.log(auction_id);    //setTimeLeftV2(auction_id);}var timerCountersAuction = {};function setAuctionTimeleft(now_time, end_time, timeleft_id, auction_id, day_label, days_label, month_label, months_label, year_label, years_label) {    if ($('auction_end_time_' + auction_id) != null)        end_time = parseInt($('auction_end_time_' + auction_id).value);    if ($('auction_now_time_' + auction_id) != null) {        if ($('auction_now_time_' + auction_id).value != '') {            now_time = parseInt($('auction_now_time_' + auction_id).value);            $('auction_now_time_' + auction_id).value = '';        }    }    if(timeleft_id == 'auction_end_timeleft'){        if ($('auction_now_time2_' + auction_id) != null) {            if ($('auction_now_time2_' + auction_id).value != '') {                now_time = parseInt($('auction_now_time2_' + auction_id).value);                $('auction_now_time2_' + auction_id).value = '';            }        }    }    if (timerCountersAuction[auction_id] == undefined) {        timerCountersAuction[auction_id] = new AuctionTimeCounter(now_time, end_time, auction_id, day_label, days_label, month_label, months_label, year_label, years_label);    } else {        timerCountersAuction[auction_id].initialize(now_time, end_time, auction_id, day_label, days_label, month_label, months_label, year_label, years_label);    }    timerCountersAuction[auction_id].setTimeleft(timeleft_id);}function updateProductName() {    $('product_name').value = $('newproduct_name').value;    $('view_auction_product').href = $('newproduct_name').attributes['href'].value;    ;    $('auction_producttab_form_listproduct').removeClassName('active');    $('auction_producttab_form_product_auction').addClassName('active');    $('auction_producttab_form_listproduct_content').style.display = 'none';    $('auction_producttab_form_product_auction_content').style.display = '';}function validNumeric(inputtext) {    var text = inputtext.value;    var Char;    var newtext = '';    var newtext1 = '';    var newtext2 = '';    var j = 0;    var i;    var is_decimal = false;    for (i = 0; i < text.length; i++) {        Char = text.charAt(i);        if ((Char != '0') || (newtext.length > 0)) {            if ((!isNaN(Char) && Char != ' ') || (Char == currencyConvert.priceFormat.decimalSymbol)) {                newtext += Char;            }        }    }    for (i = (newtext.length - 1); i >= 0; i--) {        Char = newtext.charAt(i);        if (Char != currencyConvert.priceFormat.decimalSymbol || ((Char == currencyConvert.priceFormat.decimalSymbol) && (is_decimal == false) && (newtext1.length > 0))) {            newtext1 = Char + newtext1;            if (Char == currencyConvert.priceFormat.decimalSymbol)                is_decimal = true;        }    }    var begin = newtext1.length - 1;    var varend = '';    var k = newtext1.indexOf(currencyConvert.priceFormat.decimalSymbol);    if (k != -1) {        for (i = k; i < newtext.length; i++)            varend += newtext1.charAt(i);        begin = k - 1;    }    j = 0;    for (i = begin; i >= 0; i--) {        Char = newtext1.charAt(i);        newtext2 = Char + newtext2;        j++;        if ((j == 3) && (i > 0)) {            newtext2 = currencyConvert.priceFormat.groupSymbol + newtext2;            j = 0;        }    }    if (newtext2.length == 0)        newtext2 = '0';    newtext2 += varend;    inputtext.value = newtext2;}function updateAuction(elementId, url, productId) {    var auctionUpdater = new AuctionUpdater(url);    auctionUpdater.updateInfo(elementId, productId);    setTimeout("updateAuction('" + elementId + "','" + url + "','" + productId + "')", 3000);}function checkBidPrice(bid_type) {    var bid_price = $('bid_price').value;    price = currencyConvert.getBasePrice(bid_price);    if(isNaN(price)){        alert($('empty_price').value);        return false;    } else if (price != 0) {        var min_next_price = $('min_next_price').value;        var max_next_price = $('max_next_price').value;        var current_credit = $('current_credit').value;        max_next_price = parseFloat(max_next_price);        min_next_price = parseFloat(min_next_price);        current_credit = parseFloat(current_credit);        if (current_credit < 0) {            alert($('min_credit').value);            return false;        }        if (current_credit < min_next_price || current_credit == 0) {            alert($('min_credit').value);            return false;        }        if (price < min_next_price) {            alert($('min_next_price_nonce').value);            return false;        }        if (max_next_price != 0 && parseInt(bid_type) != 0)            if (price > max_next_price) {                alert($('max_next_price_nonce').value);                return false;            }    } else {        alert($('min_next_price_nonce').value);        return false;    }    return true;}function setBackground(elementId, productId) {    var codeColorId = 'codecolor' + productId;    var codeColor = $(codeColorId).value;    var full_elementId = elementId + productId;    codeColor = parseInt(codeColor);    if (codeColor < 255)        codeColor += 1;    $(codeColorId).value = codeColor;    // $(full_elementId).style.background = 'rgb(255,' + codeColor + ',' + codeColor + ')';    setTimeout("setBackground('" + elementId + "','" + productId + "')", 10);}function placeBid(url, bid_type) {    var is_valid_bidprice = checkBidPrice(bid_type);    if (is_valid_bidprice == false)        return;    var bid_price = $('bid_price').value;    bid_price = currencyConvert.getBasePrice(bid_price);    var product_id = $('auction_product_id').value;    var current_credit = $('current_credit').value;    if (bid_price <= 0) {        return;    }    if (url.indexOf('?') == -1)        url += '?bid_price=' + bid_price;    else        url += '&bid_price=' + bid_price;    url += '&product_id=' + product_id;    url += '&current_credit=' + current_credit;    url += '&bid_type=' + bid_type;    if(bid_type == 1){        $('auction_bid_button').hide();        $('auction_autobid_button').setAttribute('disabled',true);    } else{        $('auction_autobid_button').hide();        $('auction_bid_button').setAttribute('disabled',true);    }    $('auction_bid_waitting').show();    $('msg_error').innerHTML = '';    $('msg_success').innerHTML = '';    $('msg_error').hide();    $('msg_success').hide();    $('is_bidding').value = '1';    var auctionId = $('auction_id').value;    new Ajax.Updater('results_bid_after_' + auctionId, url, {        method: 'get', onComplete: function () {            autionBidComplete();        }, onFailure: ""    });}function changeWatcher(url) {    var product_id = $('auction_product_id').value;    if (url.indexOf('?') == -1)        url += '?product_id=' + product_id;    else        url += '&product_id=' + product_id;    var is_watcher = $('auction_is_watcher').value;    url += '&is_watcher=' + is_watcher;    //$('watcher_button').hide();    //$('auction_watcher_waitting').show();    $('msg_error').innerHTML = '';    $('msg_success').innerHTML = '';    $('msg_error').hide();    $('msg_success').hide();    var auctionId = $('auction_id').value;    location.href = url;    //new Ajax.Updater('auction_watcher',url,{method: 'get', onComplete:function(){changeWatcherComplete();} ,onFailure: ""});}function changeWatcherComplete() {    $('watcher_button').show();    $('auction_watcher_waitting').hide();}function autionBidComplete() {    $('auction_bid_button').show();    $('auction_bid_button').removeAttribute('disabled');    $('auction_autobid_button').show();    $('auction_autobid_button').removeAttribute('disabled');    $('auction_bid_waitting').hide();    updateAuctionComplete();    //showTimer();    $('is_bidding').value = '0';}function showTimer(){    var customize_time = document.getElementById('customize-time');    var no_bid = document.getElementById('no-bid');    if ($('msg_success').value != ''){        customize_time.setStyle({display: 'block'});        no_bid.setStyle({display: 'none'});    } else {        customize_time.setStyle({display: 'block'});        no_bid.setStyle({display: 'none'});    }}function updateAuctionComplete() {    var auctionId;    if ($('result_auction_reset') != null) {        location.reload(true);    }    if ($('result_auction_id') != null)        auctionId = $('result_auction_id').innerHTML;    var customerId = null;    if ($('result_customer_id') != null)        customerId = $('result_customer_id').innerHTML;    var override_et =  document.getElementById('override-et');    var override_rt =  document.getElementById('override-et-time');    if (typeof(override_et) != 'undefined' && override_et != null && override_et.value > 0 && typeof(override_rt) != 'undefined' && override_rt != null && override_rt.value > 0)    {        override_et.setStyle({display: 'block'});        $('override-end-time').setStyle({display: 'block'});        //myTime('#auction_end_timeleft','Oct 25, 2016 09:11:58 +0000');        myTime('#auction_end_timeleft',$('override-et-time-new').value, true);        if ($('result_notice_ae_' + auctionId) != null) {            $('notice_auction_end_' + auctionId).innerHTML = $('result_notice_ae_' + auctionId).innerHTML;        }        var ep;        ep = new Lightboxsocial('end_popup_'+auctionId);        ep.open();    }    if ($('notice_error') != null) {        $('msg_error').innerHTML = $('notice_error').value;        if ($('notice_error').value != ''){            $('msg_error').show();        }        else{            $('msg_error').hide();        }    } else {        $('msg_error').innerHTML = '';        $('msg_error').hide();    }    if ($('notice_success') != null) {        $('msg_success').innerHTML = $('notice_success').value;        if ($('notice_success').value != '')            $('msg_success').show();        else            $('msg_success').hide();    } else {        $('msg_success').innerHTML = '';        $('msg_success').hide();    }    if ($('result_current_bid_id_' + auctionId) != null) {        $('current_bid_id_' + auctionId).value = $('result_current_bid_id_' + auctionId).innerHTML;        if(typeof($('no-bbid')) != 'undefined'){            $('update-time-end').innerHTML = '<span id="time"><span id="auction_timeleft" class="auction_timeleft"></span></span><span>Auction Timer</span>';        }    }    if ($('result_auction_end_time_' + auctionId) != null) {        $('auction_end_time_' + auctionId).value = $('result_auction_end_time_' + auctionId).innerHTML;        updateCountDownTime($('result_auction_end_time_new_' + auctionId).innerHTML);    } else if($('auction_default_end_time_' + auctionId) != null){        updateCountDownTime($('auction_default_end_time_' + auctionId).value);    }    if ($('result_auction_now_time_' + auctionId) != null) {        $('auction_now_time_' + auctionId).value = $('result_auction_now_time_' + auctionId).innerHTML;    }    if ($('result_auction_now_time2_' + auctionId) != null) {        $('auction_now_time2_' + auctionId).value = $('result_auction_now_time2_' + auctionId).innerHTML;    }    if ($('result_auction_info_' + auctionId) != null) {        $('auction_info_' + auctionId).innerHTML = $('result_auction_info_' + auctionId).innerHTML;        $('result_auction_info_' + auctionId).innerHTML = '';    }    if ($('result_customize_current_price') != null) {        $('customize_current_price').innerHTML = $('result_customize_current_price').innerHTML;        $('result_customize_current_price').innerHTML = '';    }    if ($('result_auction_info2_'+ auctionId) != null) {        $('auction_info2_'+ auctionId).innerHTML = $('result_auction_info2_'+ auctionId).innerHTML;        $('result_auction_info2_'+ auctionId).innerHTML = '';    }    if ($('result_auction_range_input') != null) {        $('auction_range_input').innerHTML = $('result_auction_range_input').innerHTML;        $('result_auction_range_input').innerHTML = '';        updateBidPrice();    }    if (customerId != null && $('result_customize_discount_price_'+customerId+'_'+auctionId) != null) {        flashInfo(true);        $('customize_discount_price_'+customerId+'_'+auctionId).innerHTML = $('result_customize_discount_price_'+customerId+'_'+auctionId).innerHTML;        $('result_customize_discount_price_'+customerId+'_'+auctionId).innerHTML = '';    }    toggleBid(false);    window.setTimeout(flashInfo,10000);}function flashInfo(small){    small = small || false;    $j = jQuery.noConflict();    var fontSize = small == true ? '17px' : '13px';    if ($('auction_timeleft') != null){        $j('#auction_timeleft').css('font-size',fontSize);    }    if($('bidder') != null){        $j('#bidder').css('font-size',fontSize);    }    if($('customize_current_price') != null){        $j('#customize_current_price .price').css('font-size',fontSize);    }}function updateCountDownTime(until_date){    myTime('#auction_timeleft',until_date, true);}function myTime(element_id, time_value, url){    url = url || true;    $j = jQuery.noConflict();    var liftoffTime = new Date(time_value);    $j(element_id).countdown('destroy');    if(url != false){				$j(element_id).countdown({			until: liftoffTime,			compact: false,			description: '',			layout: '{dn} {dl}, {hnn}:{mnn}:{snn}',			serverSync: serverTime,            expiryText: 'Checking....',			//expiryUrl: window.location.href			onExpiry: liftOff		});            } else {        $j(element_id).countdown({            until: liftoffTime,            compact: false,            description: '',            layout: '{dn} {dl}, {hnn}:{mnn}:{snn}',            serverSync: serverTime            // onExpiry: liftOff        });    }    $j.countdown.resync();}function serverTime() {    var time = null;    var url = document.getElementById('syncTime').value;    jQuery.noConflict().ajax({        url: url,        async: false,        dataType: 'text',        success: function(text) {            time = new Date(text);        },        error: function(http, message, exc) {            time = new Date();        }    });    return time;}function liftOff() {    $j = jQuery.noConflict();    var url = document.getElementById('override-et-url');	var auction_timeleft = document.getElementById('auction_timeleft');    var other_screen = $j('.timeleft1.is-countdown');    toggleBid(true);    console.log(auction_timeleft);	if(typeof(auction_timeleft) != 'undefined' && auction_timeleft != null && auction_timeleft.innerHTML == '0 Days, 00:00:00'){        if (typeof(url) != 'undefined' && url != null)		{			//window.location.href = url.value;            refreshAuctionPage(url);		} else {			//location.reload(true);            refreshAuctionPage('');		}	} else {        if(other_screen.length > 0){            other_screen.each(function (index, value) {                if($j(this).html() == '0 Days, 00:00:00'){                    refreshAuctionPage('');                }            });        }    }}function refreshAuctionPage(url){    setTimeout(function(){        var is_reload = checkIsEndAuction();        console.log(is_reload);        if(is_reload){            if(url != '')                window.location.href = url.value;            else                location.reload(true);        }    }, 5000);}function checkIsEndAuction(){    var url = document.getElementById('override-et-url');    var auction_timeleft = document.getElementById('auction_timeleft');    toggleBid(true);    if(typeof(auction_timeleft) != 'undefined' && auction_timeleft != null        && (auction_timeleft.innerHTML == '0 Days, 00:00:00' || auction_timeleft.innerHTML.indexOf('hecking') != -1)){        return true;    } else {        return false;    }}function toggleBid(enable){    var bid_button = document.getElementById('auction_bid_button');    var autobid_button = document.getElementById('auction_autobid_button');    var auction_timeleft = document.getElementById('auction_timeleft');    if(typeof(auction_timeleft) != 'undefined' && auction_timeleft != null        && (auction_timeleft.innerHTML == '0 Days, 00:00:00' || auction_timeleft.innerHTML.indexOf('hecking') != -1)){        enable = true;    }    if(typeof(bid_button) != 'undefined' && bid_button != null){        if(enable)            bid_button.setAttribute('disabled', true);        else            bid_button.removeAttribute('disabled');    }    if(typeof(autobid_button) != 'undefined' && autobid_button != null){        if(enable)            autobid_button.setAttribute('disabled', true);        else            autobid_button.removeAttribute('disabled');    }}function updateAuctionListComplete() {    var productId;    if ($('result_product_id') != null)        productId = $('result_product_id').innerHTML;    var end_time_list = $('result_auction_end_time_new_'+productId) != null ? $('result_auction_end_time_new_'+productId).innerHTML : null;    if ($('result_auction_info_' + productId) != null) {        $('auction_info_' + productId).innerHTML = $('result_auction_info_' + productId).innerHTML;        $('result_auction_info_' + productId).innerHTML = '';    }    if ($('results_update_auction_' + productId) != null)        $('results_update_auction_' + productId).innerHTML = '';    console.log(end_time_list);    if(end_time_list != null){        console.log(end_time_list);        myTime('#timeleft_'+productId,end_time_list);    }}function openTabs(evt, tabName) {    var i, x, tablinks;    x = document.getElementsByClassName("auction_tabs");    for (i = 0; i < x.length; i++) {        x[i].style.display = "none";    }    tablinks = document.getElementsByClassName("ctabs");    for (i = 0; i < x.length; i++) {        tablinks[i].className = tablinks[i].className.replace(" current", "");    }    document.getElementsByClassName(tabName)[0].style.display = "block";    evt.currentTarget.className += " current";}function updateBidPrice() {	if(document.getElementById('rangeinput') != null) {        var _step = $('rangeinput').value;        var _current_price = document.getElementById('auction_current_price').value;        var _bid_price = document.getElementById('bid_price');        _bid_price.value = parseInt(parseInt(_current_price) + parseInt(_step));    }}var _startTimer;function startTimer(duration, display) {    var timer = duration, minutes, seconds, hours, days;    _startTimer = setInterval(function () {        if(timer <= 0){            window.clearInterval(_timer);            display.textContent = "00 days, 00:00:00";            return;        }        days = parseInt(timer / 86400, 10);        if(days > 0){            hours = parseInt((timer - 86400*days)/3600, 10);        } else {            hours = parseInt(timer/3600, 10);        }        minutes = parseInt((timer % 3600)/60, 10);        seconds = parseInt(timer % 3600 % 60, 10);        days = days < 10 ? "0" + days : days;        hours = hours < 10 ? "0" + hours : hours;        minutes = minutes < 10 ? "0" + minutes : minutes;        seconds = seconds < 10 ? "0" + seconds : seconds;        display.textContent = days + " days, " + hours + ":" + minutes + ":" + seconds;        if (--timer <= 0) {            window.clearInterval(_startTimer);            display.textContent = "00 days, 00:00:00";            var url = document.getElementById('override-et-url');            if (typeof(url) != 'undefined' && url != null)            {                window.location.href = url.value;            }            return;        }    }, 1000);}function closeTimer(display){    window.clearInterval(_startTimer);    display.textContent = "";}
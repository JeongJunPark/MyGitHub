(function($) {

// ajax시작 전
$(document).ajaxStart(function(){
	$('.apply_loading').css('display','block');
});
	
// ajax시작 후
$(document).ajaxStop(function(){
    $('.apply_loading').css('display','none');
});

/*
 * main 
*/
if($('#mainPage').length){

	$(document).ready(function() {

        $('.tablinks').click(function() {
			var elm_id = $(this).data('id');
			var $tab_content, $tab_main;

			$tab_main = $(".main_s2");
			$tab_content = $(".tabcontent");

			$tab_content.each(function(index, item) {
				$(item).hide();
			});

			$('#' + elm_id).show();

			if (elm_id == 'celib-select') {
				$tab_main.addClass('bg_type2');
			} else {
				$tab_main.removeClass('bg_type2');
			}

			$('.tablinks').each(function(index, item) {
				$(item).removeClass('active');
				$(item).attr('style', '');
				$(item).find('hr').css('visibility', 'hidden');
			});

			$(this).addClass('active');
			$(this).find('hr').css('visibility', 'visible');

		});

		$('.tablinks').hover(function() {
			if (!$(this).hasClass("active")) {
				$(this).css('opacity', '1');
			}
		}, function() {
			if (!$(this).hasClass("active")) {
				$(this).css('opacity', '0.4');
			}
		});

		$('#tab-locations').click();

	});

	var swiper = new Swiper(".tab-swiper-container1", {
		slidesPerView: 3,
		spaceBetween: 29,

		/* 
		pagination: {
			el: ".swiper-pagination",
			clickable: true,
		},
		navigation: {
			nextEl: ".swiper-button-next",
			prevEl: ".swiper-button-prev"
		}
		*/
	});

	var swiper = new Swiper(".tab-swiper-container2", {
		slidesPerView: 3,
		spaceBetween: 29,
		/*
		pagination: {
			el: ".swiper-pagination",
			clickable: true,
		},
		navigation: {
			nextEl: ".swiper-button-next",
			prevEl: ".swiper-button-prev"
		}
		*/
	});

	var swiper = new Swiper(".swiper-container2", {
		slidesPerView: 1,
		spaceBetween: 30,
		pagination: {
			el: ".swiper-pagination2",
			clickable: true,
		},
		navigation: {
			nextEl: ".swiper-button-next2",
			prevEl: ".swiper-button-prev2"
		}
	});

/*
	var swiper = new Swiper(".swiper-container3", {
		slidesPerView: 1,
		spaceBetween: 30,
		pagination: {
			el: ".swiper-pagination3",
			clickable: true,
		}
		,
		navigation: {
			nextEl: ".swiper-button-next3",
			prevEl: ".swiper-button-prev3"
		}
	});
*/
}


/*
 *  sub page
*/
$(document).ready(function(){
    if($(".house_view").length){
        /*
		var space_thumb = new Swiper(".small_gallery", {
	    	//loop: true,
	    	spaceBetween: 10,
	    	slidesPerView: 9,
			touchRatio:0,
            slideToClickedSlide: true,
	    	//freeMode: true,
	    	watchSlidesVisibility: true,
	    	watchSlidesProgress: true,
	    });
        */
	    var space_swiper = new Swiper(".big_gallery", {
	      loop: true,
	      spaceBetween: 10,
	    });
	
		// get all bullet elements
		var bullets = $('#swiper-left-tab > li');
		var thumbs = $('.small_gallery > img');
		
		// swiper 오른쪽 메뉴 클릭 이벤트
		$.each(bullets, function (index, value) {
			$(this).on('click', function(){
	    	    space_swiper.slideTo($(this).data('slide'), 1000);
	    	});
		});
        // swiper 오른쪽 메뉴 클릭 이벤트
		$.each(thumbs, function (index, value) {
			$(this).on('click', function(){
	    	    space_swiper.slideTo($(this).data('slide'), 1000);
	    	});
		});
	
		// swiper 변경시 이벤트
		space_swiper.on('slideChange', function () {

            var slide_idx = this.activeIndex;

			bullets.removeClass("active");
			thumbs.removeClass("active");

			$.each(bullets, function (index, value) {
			    if($(this).data('slide') == slide_idx) {
			        $(this).addClass("active");
			        return false;
			    }
			});
            $.each(thumbs, function (index, value) {
			    if($(this).data('slide') == slide_idx) {
			        $(this).addClass("active");
			        return false;
			    }
			});
		}); 
		
		// 개인룸 이미지 팝업 열기 + 이미지 선택시 변경
		(openModal = function(modal_name) {
			
			// 임시주석
			return false;
/*
			$('#modal').fadeIn(300);
			$("." + modal_name).fadeIn(300);

            var loc = $(location).attr('pathname').split(".");
                loc = loc[0];
            var modal_idx = modal_name.charAt(modal_name.length-1);

            var $thumbs = $("." + modal_name).find('.small_con > img');
            var $img_elm= $("." + modal_name).find('.big_con > img');

            $.each($thumbs, function (index, value) {
                $(this).off().on('click', function(){
                    var img_idx = index + 1;
                    var img_addr = '/assets/images/sub'+loc+loc+'_s6_drawings'+modal_idx+'_gbig'+img_idx+'.jpg';
                    $img_elm.attr('src', img_addr);

			        $thumbs.removeClass("active");
                    $(this).addClass("active");
                });
            });
*/
		});

		// 개인룸 이미지 팝업 닫기
		$('.sub_mod_close').click(function() {
			$('.modal_con').fadeOut(300);
			$('#modal').fadeOut(300);
		});
			
    }
});

/*
 *  common
*/
	// 모바일 메뉴 클릭시 한개만 활성화
	$('input[type="checkbox"][name="accordion-1"]').click(function() {
		if($(this).prop('checked')) {
			$('input[type="checkbox"][name="accordion-1"]').prop('checked',false);
			$(this).prop('checked',true);
		}
	});

	(text_trim = function(obj) {
		if($('#email'.length)){
			var a = $('#phone').val().replace(/ /gi, '');
			$('#phone').val(a);
		}
		
		if($('#email'.length)){
			var a = $('#email').val().replace(/ /gi, '');
			$('#email').val(a);
		}
        
    });
	

	// 개인정보 이용동의 default
	$('.accordion_body').slideUp();

	// 개인정보 이용동의 접기/펴기
	$('.accordion_head').find('.arrow').on('click', function() {
		if (!$('.accordion_body').is(':visible')) {
			$('.accordion_body').slideDown();
			$(this).removeClass("down");
			$(this).addClass("up");
		} else {
			$('.accordion_body').slideUp();
			$(this).removeClass("up");
			$(this).addClass("down");
		}
	});

	// 체크박스 전체선택 및 전체해제 PC
	$("#apply_all").click(function() {
		if ($("#apply_all").is(":checked")) {
			$(".apply_chk").prop("checked", true);
		} else {
			$(".apply_chk").prop("checked", false);
		}
	});

	// 한개의 체크박스 선택 해제시 전체선택 체크박스도 해제 PC
	$(".apply_chk").click(function() {
		if ($("input[name='chk']:checked").length == 2) {
			$("#apply_all").prop("checked", true);
		} else {
			$("#apply_all").prop("checked", false);
		}
	});

	//var is_mobile = isMobile();
	/*
	$(window).resize(function(){

	    is_mobile = isMobile();

		// PC <-> MOBILE 변경시 팝업 닫기
		if(is_mobile){
			$('#applyWrap').dialog('close');
		}else{
			$('#mo-applyWrap').dialog('close');
		}
	});
	*/


	$(document).ready(function(){
		$('#menuTopbox').click(function() {
			$('#menu').show();
		});
	});

	// 개인정보 수집 및 이용동의
	$( '.privacy' ).click(function() {
		var url = '/privacy';
		
		var is_mobile = isMobile();
		if(is_mobile){
			var pb_height = 400;
			var pb_width = 300;

		}else{
			var pb_height = 450;
			var pb_width = 550;
		}
	
		 $('<div id="PrivacyDiv">').dialog({
				
			dialogClass: 'privacy-dialog',
			modal: true,
			open: function () {

				$(".ui-widget-overlay").css({
					opacity: 0.5
				});

				$(this).load(url);
			},

			close: function (e) {
				$(this).empty();
				$(this).dialog('destroy');
			},
			
			height: pb_height,
			width: pb_width
		});
	});

	// 개인정보 제3자 제공동의
	$( '.terms' ).click(function() {
		
		var url = '/privacy/terms';

		var is_mobile = isMobile();
		if(is_mobile){
			var pb_height = 400;
			var pb_width = 300;

		}else{
			var pb_height = 450;
			var pb_width = 550;
		}

		 $('<div id="TermsDiv">').dialog({
				
				dialogClass: 'terms-dialog',
				modal: true,
				open: function () {

					$(".ui-widget-overlay").css({
						opacity: 0.5
					});

					$(this).load(url);
				},

				close: function (e) {
					$(this).empty();
					$(this).dialog('destroy');
				},

				height: pb_height,
				width: pb_width
		});
	});

	// 메인페이지 투어신청 팝업 오픈
	$( '.myBtn' ).click(function() {

		$('#applyWrap').dialog('open');
		$('#applyWrap').draggable();
	
		// 서브페이지 투어신청시 지점선택 default
		var house_val = $("#house_id").val();	
		var loc_house = $(location).attr('pathname').split(".");
        loc = loc_house[0];
		
		if(loc == '/soonra'){
			 $("#house_id").val('1');
		}else if(loc == '/yeoui'){
			 $("#house_id").val('2');
		}else if(loc == '/eunpyong'){
			 $("#house_id").val('3');
		}else if(loc == '/yongsan'){
			 $("#house_id").val('4');
		}else{
			$("#house_id").val('');
		}
	});
	
	$( "#applyWrap" ).dialog({
		dialogClass: 'celib-dialog',
        autoOpen : false, 
        modal : true, 
        resizable : false,
		open: function(event, ui) {
			$("body").css({ overflow: 'hidden' })
		 },
		beforeClose: function(event, ui) {
			$("body").css({ overflow: 'inherit' })
		 },
		width: 'auto'
	});

	$('#btn_close').click(function() {

		$('#applyWrap').dialog('close');

		var frm_tour = $("#frm-tour");
		frm_tour[0].reset();	// 초기화
	});


	// 신청완료 팝업닫기
	$('.btn_confirm').click(function() {
		$('.apply_clear').css('display','none');
	});

	// 메인페이지 투어신청 팝업 오픈
	$( '#celib_intro' ).click(function() {
		$('#celib-intro-wrap').dialog('open');
		$('#celib-intro-wrap').draggable();
	});
	
	//celib-intro 팝업
	$( '#celib-intro-wrap' ).dialog({
		dialogClass: 'intro-dialog',
        autoOpen : false, 
        modal : true, 
        resizable : false,
		open: function(event, ui) {
			$("body").css({
					'overflow': 'hidden'
					,'touch-action': 'none'
					,'-ms-touch-action': 'none'
			})

			$('.ui-widget-overlay').on('click', function() {
				$('#celib-intro-wrap').dialog('close');
			});
		},

		beforeClose: function(event, ui) {
			$("body").css({ 
				'overflow': 'scroll' 
				,'touch-action': 'auto'
				,'-ms-touch-action': 'auto'
			})
		},

		width: 'auto'
	});
	
	// 셀립소개
	$('#celib-intro-wrap').click(function() {
		$('#celib-intro-wrap').dialog('close');
	});

    //모바일 체크
    function isMobile()
    {
           return (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ); 
    }

	// 이메일형식 체크함수
	function email_chk(str) {

        var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
        if (!filter.test(str)) {
            return true;
        } else {
            return false;
        }
    }

	// 특수문자 체크함수
	function special_chk(str) {

		var special_pattern = /[`~!@#$%^&*|\\\'\";:\/?]/gi; 
		
		if(special_pattern.test(str) == true) { 
			return true;
		} else { 
			return false; 
		} 
	}

	// 휴대폰번호 체크함수
	function phone_chk(str) {

		var regExp = /^01(?:0|1|[6-9])-(?:\d{3}|\d{4})-\d{4}$/;
		
		if(regExp.test(str) == true) { 
			return true;
		} else { 
			return false; 
		} 
	}

	var apply_btn = $("#tourApply_btn");

	apply_btn.click(function(){

		var house_id	= $("#house_id").val();			// 지점정보
		var name		= $("#name").val();				// 이름
		var phone		= $("#phone").val();			// 휴대폰
		var tour_date	= $("#tour_date").val();		// 투어 희망일
		var tour_time	= $("#tour_time").val();		// 투어 희망시간
		var period		= $("#period").val();			// 입주 희망기간
		var email 		= $("#email").val();			// 이메일
		var agree_chk_A	= $("input:checkbox[id=apply_chk1]").is(':checked');	// 개인정보 이용동의
		var agree_chk_B	= $("input:checkbox[id=apply_chk2]").is(':checked');	// 개인정보 제3자 제공동의


		if(!house_id){
			alert('지점을 선택해주세요!');
			return;
		}

		if(!phone){
			alert('휴대폰번호를 입력해주세요!');
			return;
		}

		if(email.length > 0){
			if(email_chk(email) == true) {
				alert('이메일 형식을 정확하게 입력해주세요!');
				return;
			}
		}

		if(!agree_chk_A || !agree_chk_B){
			alert('개인정보 이용동의에 모두 체크해주세요!');
			return;
		}

		$("#frm-tour").submit();
	});


	var frm_tour = $("#frm-tour");
	
	// 투어 예약하기
	frm_tour.submit(function(e) {
	
	    e.preventDefault(); // avoid to execute the actual submit of the form.
	
	    var form = $(this);
		var btn_close = $('#applyWrap');
		
	    $.ajax({
	        type: "POST",
			url: '/api/Tours/apply',
	        data: form.serialize(), // serializes the form's elements.
	        success: function(data)
	        {
                if(data.code == 0) {
	                alert('신청 실패. 관리자에 문의하세요.');
					return;
                }else if(data.code == -1) {
	                alert('휴대폰번호를 입력해주세요.');
					return;
                }else if(data.code == -2) {
	                alert('유효한 핸드폰 번호가 아닙니다. 다시 확인해주세요.');
					return;
                }else if(data.code == -3) {
	                alert('지점을 선택해주세요.');
					return;
                }else if(data.code == -4) {
	                alert('유효한 이메일이 아닙니다. 다시 확인해주세요.');
					return;
                }else if(data.code == -5) {
	                alert('저장 에러. 관리자에 문의하세요.');
					return;
                } else if(data.code == -6) {
	                alert('이메일 전송실패.');
					return;
                }else {
					$('.apply_clear').show();
					btn_close.dialog('close');
					frm_tour[0].reset();
                }
	        }
    		,error:function(e){
    			alert('신청 실패.');
    		}
    		,timeout:100000
	    });

	});

	// jquery alert창
	jQuery.jQueryAlert = function (msg) {
		 var $messageBox = $.parseHTML('<div id="ready_alertBox"></div>');
		 $("body").append($messageBox);
 
		 $($messageBox).dialog({
			 title:'안내',
			 dialogClass: 'alert-dialog',
			 open: $($messageBox).append(msg),
			 open: function(event, ui) {
			 	$("body").css({ overflow: 'hidden' })
			 },
			 beforeClose: function(event, ui) {
			 	$("body").css({ overflow: 'inherit' })
			 },
			 autoOpen: true,
			 modal: true,
			 buttons: {
				 OK: function () {
					 $(this).dialog('close');
				 }
			 }
		 });
	 };
 
	 $('.ready_to_faq').on("click", function(){
		 $.jQueryAlert("준비중입니다.");
	 });

})(jQuery); // End of use strict

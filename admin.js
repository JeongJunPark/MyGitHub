var TOUR_STATE = {
    '0':'투어신청'
    ,'1':'메일발송'
    ,'2':'재확인문자'
    ,'3':'투어예정'
    ,'4':'투어완료'
    ,'7':'투어취소'
    ,'5':'입주논의'
    ,'6':'계약완료'
};
var residence_status = [
    '가족과 함께'
    ,'친구와 함께'
    ,'혼자서 거주'
    ,'셰어하우스/코리빙'
];
var residence_type = [
    '오피스텔'
    ,'원룸'
    ,'투룸이상'
    ,'아파트'
    ,'주택'
];
var path = [
    '선택안함'
    ,'홈페이지'
    ,'인스타그램'
    ,'전화'
    ,'카카오톡'
    ,'에어비엔비'
    ,'메일'
    ,'워크인'
    ,'거주자 지인'
    ,'직원 지인'
];

// ajax시작 전
$(document).ajaxStart(function(){
	$('.apply_loading').css('display','block');
});
	
// ajax시작 후
$(document).ajaxStop(function(){
    $('.apply_loading').css('display','none');
});


(function($) {
    "use strict"; // Start of use strict

    

    // Scroll to top button appear
    $(document).scroll(function() {
        var scrollDistance = $(this).scrollTop();
        if (scrollDistance > 100) {
            $('.scroll-to-top').fadeIn();
        } else {
            $('.scroll-to-top').fadeOut();
        }
    });

    // Smooth scrolling using jQuery easing
    $(document).on('click', 'a.scroll-to-top', function(event) {
        var $anchor = $(this);
        $('html, body').stop().animate({
            scrollTop: ($($anchor.attr('href')).offset().top)
        }, 1000, 'easeInOutExpo');
        event.preventDefault();
    });

    // Call dhe dataTables jQuery plugin
    $(document).ready(function() {

        $('#dataTable').DataTable({
            "order": [[ 0, "desc" ]]
        });

        $('#tradeTable').DataTable({
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
			//"scrollX": true,
			//"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            'ajax': {
              'url':'/api/trades'
            },
			'columns': [
      			{ data: "id" },
      			{ data: 'contract_id' },
      			{ data: 'number' },
      			{ data: 'iacct_nm' },
      			{ data: 'user_name',
                    render: function(data, type, row) {
						return "<span>"+data+"("+row.user_phone+")</span>";	
                    }
                },
      			{ data: 'tr_amt' },
      			{ data: 'deposit_time' },
      			{ data: 'trade_type' },
            ],
            "order": [[ 0, "desc" ]]
        });

        var dialog_apply, form_apply, dialog, form, dialog_state, tour_date_form, tour_date_dialog, sel_row;
        // tour page start
        if($('#tour-page').length){ 
            var tour_table = $('#tourTable').DataTable({
		    	"scrollX": true,
                "scrollXInner": "3000px",
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
		    	//"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                'ajax': {
                  'url':'/api/tours'
                },
		    	'columns': [
      	    		{ data: 'id' },
      	    		{ data: 'name',
                        render: function(data, type, row) {
							if(data === null) {
		    					return "<span>미등록</span><button class='modify-btn' data-id='"+row.id+"'>수정</button>";	
							}else {
								return "<span>"+data+"</span><button class='modify-btn' data-id='"+row.id+"'>수정</button>";	
							}
		    			}
                    },
      	    		{ data: 'h_name_kr'},
      	    		{ 
		    			data: 'state',
		    			render: function(data, type, row) {
                            var $select = $("<select data-id='"+row.id+"' class='state-select'><option value='0'>투어신청</option><option value='1'>메일발송</option><option value='2'>재확인문자</option><option value='3'>투어예정</option><option value='4'>투어완료</option><option value='7'>투어취소</option><option value='5'>입주논의</option><option value='6'>계약완료</option></select>");
                            $select.find('option[value="'+data+'"]').attr('selected', 'selected');
                            //var $icon = $("<span class='ui-icon ui-icon-note'></span>");
                            var $icon = $( "<button data-id='"+row.id+"' class='state-btn'></button>" ).button({ text:false,icons: { primary: "ui-icon-note", secondary: null }}); 
                            var $div = $("<div></div>");
                            $div.append($select[0].outerHTML + $icon[0].outerHTML);
                            return $div[0].outerHTML
		    				//return "<span>"+state[data]+"</span><button class='memo-btn' id='"+row.id+"'>메모</button>";	
		    			}
		    		},
      	    		{ data: 'phone' },
      	    		{ data: 'email' },
      	    		{ data: 'hope_movein' },
      	    		{ 
                        data: 'tour_date',
		    			render: function(data, type, row) {
							
							var tour_dt = "";

							if(data === null) {			// 투어날짜 data가 null일 경우
								return "<span>미등록</span><button class='tour-date-apply-btn'>등록</button>";	
							}else {						// 투어날짜 data가 기등록 되어있을 경우
                                tour_dt = data + " " + row.tour_time + ":00 <button class='tour-date-apply-btn'>수정</button>";
								return "<span>" + tour_dt + "</span>";	
                            }
		    			}
                    },
     	    		{ data: 'created_at' },
      	    		{ data: 'hope_period' },
                    { 
		    			data: 'path',
		    			render: function(data, type, row) {
		    				return path[data];	
		    			}
		    		},
      	    		{ data: 'current_residence',
                        render: function(data, type) {
                        	if (type === 'display') {
                                var str  = '';
                                if(data==-1) str = '답안함';
                                else if(data==null) str = '';
                                else  str = data;
                        	    return '<span> ' + str + '</span> ';
                            }
                            return data;
                        }
                    },
                    { data: 'current_residence_status',
                        render: function(data, type) {
                        	if (type === 'display') {
                                var str  = '';
                                if(data==-1) str = '답안함';
                                else if(data==null) str = '';
                                else if($.inArray(data, ["1","2","3","4"]) >=0 ) str = residence_status[data];
                                else  str = data;
                        	    return '<span> ' + str + '</span> ';
                            }
                            return data;
                        }
                    },
                    { data: 'current_residence_type', 
                        render: function(data, type) {
                        	if (type === 'display') {
                                var str  = '';
                                if(data==-1) str = '답안함';
                                else if(data==null) str = '';
                                else  str = data;
                        	    return '<span> ' + str + '</span> ';
                            }
                            return data;
                        }
                    },
      	    		{ data: 'age', 
                        render: function(data, type) {
                        	if (type === 'display') {
                                var str  = '';
                                if(data==-1) str = '답안함';
                                else if(data==null) str = '';
                                else  str = data;
                        	    return '<span> ' + str + '</span> ';
                            }
                            return data;
                        }
                    },
      	    		{ data: 'gender',
		    			render: function(data, type) {
                        	if (type === 'display') {
                                var str  = '';
                                if(data==1) str = '남';
                                else if(data==2) str = '여';
                                else if(data==-1) str = '답안함';
                                else if(data==null) str = '';
                        	    return '<span> ' + str + '</span> ';
                            }
                            return data;
                        }
                    },
      	    		{ data: 'job', 
                        render: function(data, type) {
                        	if (type === 'display') {
                                var str  = '';
                                if(data==-1) str = '답안함';
                                else if(data==null) str = '';
                                else  str = data;
                        	    return '<span> ' + str + '</span> ';
                            }
                            return data;
                        }
                    },
                    { 
		    		 	"class":          "survey-control",
                		"orderable":      false,
                		"data":           'survey',
		    			render: function(data, type) {
                        	if (type === 'display') {
		    					var tmp = [];
		    					if(data) {
		    						var obj= JSON.parse(data); 
		    						$.each(obj, function (key, val) {
		    							tmp.push(qa_list[key]['a'][val]);	
		    							//tmp.push(val);	
		    						});
		    					}
                        	    return '<span> ' + tmp.join(", ") + '</span> ';
                        	}
 
                        	return data;
		    			}
                	},
      	    		{ data: 'etc' }
      	    	],
                columnDefs: [
			        {orderable: false, targets :[2, 3]}
		        ],
                initComplete: function () {
		        	this.api().columns().every( function (e) {
		        		if(e==2  ){
		        			var column = this;
		        			var col_name = '지점';
		        			var select = $('<select><option value="">'+col_name+'</option></select>')
		        			.appendTo( $(column.header()).empty() )
		        			.on( 'change', function () {
		        				var val = $.fn.dataTable.util.escapeRegex(
		        					$(this).val()
		        				);
		        				column.search( val, true, false ).draw();
		        			} );

		        			column.data().unique().sort().each( function ( d, j ) {
		        				select.append( '<option value="'+d+'">'+d+'</option>' )
		        			} );
		        		}
                        else if(e==3  ){
		        			var column = this;
		        			var col_name = '상태';
		        			var select = $('<select><option value="">'+col_name+'</option></select>')
		        			.appendTo( $(column.header()).empty() )
		        			.on( 'change', function () {
		        				var val = $.fn.dataTable.util.escapeRegex(
		        					$(this).val()
		        				);
		        				column.search( val, true, false ).draw();
		        			} );

		    				$.each(TOUR_STATE, function (key, val) {
		        			    select.append( '<option value="'+key+'">'+val+'</option>' );
		        			});
		        		}
		        	} );
		        },
                "order": [[0, 'desc']]
            });

            dialog_apply = $( "#apply-form" ).dialog({
    	      autoOpen: false,
    	      height: 900,
    	      width: 400,
    	      modal: true,
    	      buttons: {
    	        "save": function(){
		    		form_apply.submit();
		    	},
    	        Cancel: function() {
    	          dialog_apply.dialog( "close" );
    	        }
    	      },
    	      close: function() {
    	        form_apply[ 0 ].reset();
    	        //allFields.removeClass( "ui-state-error" );
    	      }
    	    });

            $( "#tour_date" ).datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'yy-mm-dd',
            });

            $( "#hope_movein" ).datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'yy-mm-dd',
            });

			$( "#apply_tour_date" ).datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'yy-mm-dd',
            });

		    dialog = $( "#dialog-form" ).dialog({
    	      autoOpen: false,
    	      height: 840,
    	      width: 400,
    	      modal: true,
    	      buttons: {
    	        "save": function(){
		    		//form.submit();
                    dialog_action();
		    	},
                "delete": function(){
                    dialog_action('remove');
		    	},
    	        Cancel: function() {
    	          dialog.dialog( "close" );
    	        }
    	      },
    	      close: function() {
    	        form[ 0 ].reset();
    	        //allFields.removeClass( "ui-state-error" );
    	      }
    	    });


			

			// 투어날짜 등록 dialog
		    tour_date_dialog = $( "#tour-date-dialog-form" ).dialog({
    	      
			  autoOpen: false,
    	      height: 340,
    	      width: 400,
    	      modal: true,
    	      buttons: {
    	        "save": function(){
                    tour_dialog_action();
		    	},
    	      Cancel: function() {
    	          tour_date_dialog.dialog( "close" );
    	        }
    		  },

    	      close: function() {
    	        tour_date_form[ 0 ].reset();
    	      }
    	    });

    	    form_apply = $("#tour-apply-form" ).on( "submit", function( event ) {

		    	var $t, t, chk=true;
		    	$(this).find("input, select").each(function(i) {
            	    $t = $(this);

            	    if($t.prop("required")) {
            	        if(!jQuery.trim($t.val())) {
            	            t = jQuery("label[for='"+$t.attr("id")+"']").text();
            	            chk = false;
            	            $t.focus();
            	            alert(t+" 필수 입력입니다.");
            	            return false;
            	        }
            	    }
            	});

		    	if(!chk) {
		    		return false;
		    	}

                var url = '/api/tours/apply';

		    	$.ajax({
		    	    type: "POST",
		    	    url: url,
		    	    data: form_apply.serialize(), // serializes the form's elements.
		    	    success: function(data)
		    	    {
                    //console.log(data);
                        if(data.code == true) {
		    	            alert('저장 완료. ');
                            tour_table.ajax.reload( null, false );
    	          		    dialog_apply.dialog( "close" );
		    	        }else {
		    	            alert(data.msg);
                            return false;
		    	        }
		    	    }
		    	    ,complete:function(){
    	          		dialog_apply.dialog( "close" );
		    	    }
		    	    ,error:function(e){
		    	        alert('저장 실패.');
		    	    }
		    	});
		    });
                    
    	    form					= dialog.find( "form" );					// 투어신청폼
			tour_date_form			= tour_date_dialog.find( "form" );			// 투어날짜 등록폼

            function dialog_action (action="update") {

		    	var url = '/api/tours/'+action;

		    	$.ajax({
		    	    type: "POST",
		    	    url: url,
		    	    data: form.serialize(), // serializes the form's elements.
		    	    success: function(data)
		    	    {
		    	        if(data == true) {
		    	            alert('저장 완료. ');
    	          		    dialog.dialog( "close" );
                            tour_table.ajax.reload( null, false );
		    	        }else if(data == -1) {
		    	            alert('저장 실패. 관리자에 문의하세요.');
		    	        }
		    	    }
		    	    ,complete:function(){
    	          		dialog.dialog( "close" );
		    	    }
		    	    ,error:function(e){
		    	        alert('저장 실패.');
		    	    }
		    	});

    	    };

			// 투어날짜 등록
			function tour_dialog_action() {
				  
				var tour_date = $('#apply_tour_date').val();	// 투어일자
				var tour_time = $('#apply_tour_time').val();	// 상태
				
				if(!tour_date){
					alert('투어신청일을 선택해주세요!');
					return;
				}

				if(!tour_time){
					alert('투어시간을 선택해주세요!');
					return;
				}

                gapi.auth2.getAuthInstance().isSignedIn.listen(checkGoogleLogin);
                checkGoogleLogin(gapi.auth2.getAuthInstance().isSignedIn.get());
			}


			function checkGoogleLogin(isSignedIn) {
                //console.log('isSignedIn::'+isSignedIn);
                if (isSignedIn) {
                	tour_reserv();
                } else {
                    gapi.auth2.getAuthInstance().signIn();
                }
            }


			function tour_reserv(){

				var url = '/api/tour_state/add';

		    	$.ajax({
		    	    type: "POST",
		    	    url: url,
		    	    data: tour_date_form.serialize(), // serializes the form's elements.
		    	    success: function(data)
		    	    {
		    	        if(data.success) {

							addToGoogleCalendar();

    	          			tour_date_dialog.dialog( "close" );
                            tour_table.ajax.reload( null, false );

		    	            alert('저장 완료. ');

		    	        }else if(data == -1) {
		    	            alert('저장 실패. 관리자에 문의하세요.');
		    	        }
		    	    }
		    	    ,complete:function(){
						tour_date_dialog.dialog( "close" );
		    	    }

		    	    ,error:function(e){
		    	        alert('저장 실패.');
		    	    }
		    	});
			}

			function addToGoogleCalendar() {

                //테스트 서버에서는 등록 안함  
                var test_url = ['stg.celib.kr', '192.168.0.5', 'localhost', 'woozooin.iptime.org']
                if(test_url.includes(window.location.host)){
                    return false;
                }

				var tour_date = $('#apply_tour_date').val() +" "+$('#apply_tour_time').val()+":00";
				var row = sel_row;

                //console.log('addToGoogleCalendar::');
                //console.log(row);
                
                var wootaTime = new Date(tour_date);
                wootaTime.setTime(wootaTime.getTime() + 9 * 3600000)
                var startStr = wootaTime.toISOString().substr(0, 19)
                wootaTime.setTime(wootaTime.getTime() + 3600000);
                var endStr = wootaTime.toISOString().substr(0, 19);

                var event = {
                    'summary': '[' + row.h_name_kr + '] ' + row.phone,
                    'description': '투어예약 => 지점: ' + row.h_name_kr + ', 연락처: ' + row.phone ,
                    'start': {
                        'dateTime': startStr,
                        'timeZone': 'Asia/Seoul'
                    },
                    'end': {
                        'dateTime': endStr,
                        'timeZone': 'Asia/Seoul'
                    },
                    'reminders': {
                        'useDefault': true
                    }
                };

                //console.log(event);
                //console.log(gapi.client.calendar);
                var request = gapi.client.calendar.events.insert({
                    'calendarId': 'celib.kr_rhojomrj3cie3r5nh9h7ugg1jo@group.calendar.google.com',
                    'resource': event
                });

                request.execute(function(event) {
                    //console.log('execute::');
                    //console.log(event);
                    if(event.message) {
                        alert('error=> code: '+event.code+', message: '+event.message);
                    }
                });

                /*
                var xmlStr = '<atom:entry xmlns:atom="http://www.w3.org/2005/Atom" xmlns:gd="http://schemas.google.com/g/2005"><atom:category scheme="http://schemas.google.com/g/2005#kind" term="http://schemas.google.com/contact/2008#contact"/><gd:name><gd:fullName>$display</gd:fullName></gd:name><atom:content type="text">$display</atom:content><gd:phoneNumber rel="http://schemas.google.com/g/2005#work" primary="true">$phone</gd:phoneNumber></atom:entry>';
                xmlStr = xmlStr.replace(/\$display/gi, '[' + row.name + ']' + row.h_name_kr + '투어');
                xmlStr = xmlStr.replace('$phone', row.phone);

                $.ajax({
                    url: 'https://content.googleapis.com/m8/feeds/contacts/default/full',
                    method: 'POST',
                    headers: {
                        'authorization': 'Bearer ' + gapi.client.getToken().access_token,
                        'Content-Type': 'application/atom+xml',
                        'GData-Version': '3.0'
                    },
                    data: xmlStr
                }).done(function() {});
                */
            } 

            dialog_state = $( "#dialog-state" ).dialog({
    	      autoOpen: false,
    	      height: 500,
    	      width: 600,
    	      modal: true
    	    });

            tour_table.on("change", ".state-select", function (e) {
                //console.log($(this).data('id'));
                var id = $(this).data('id');
                var state = $("option:selected", this).val();
                //var row =  tour_table.row( $(this).parent().parent() ).data();

                $.ajax({
		    	    type: "POST",
		    	    url: '/api/tours/change_state',
		    	    data: {'id':id, 'state':state},
		    	    success: function(data)
		    	    {
                        if(data.success) {
                            alert('수정되어있습니다.');
							tour_table.ajax.reload( null, false );
                        }else {
                            alert('수정 실패.');
                        }
		    	    }
		    	    ,error:function(e){
                        alert('통신 에러.');
		    	    }
		    	});

    	    });

            $('#tour-apply-btn').on("click", function (e) {
    	      	dialog_apply.dialog( "open" );
                //console.log($(this).data('id'));
    	    });
			
		    tour_table.on("click", ".modify-btn", function (e) {
    	      	dialog.dialog( "open" );
                
                var row =  tour_table.row( $(this).parent().parent() ).data();

                $('#tour-form > #id').val(row.id);
                if(row.name) {
                    $('#tour-form input[name="name"]').val(row.name);
                }
                $('#tour-form input[name="current_residence"]').val(row.current_residence);

                if($.inArray(row.current_residence_status, ['1','2','3','4']) >= 0) {
                    $('#tour-form select[name="current_residence_status"]').val(row.current_residence_status);
                }else if (row.current_residence_status) {
                    $('#tour-form select[name="current_residence_status"]').val('기타');
                    $('#tour-form input[name="current_residence_status_etc"]').val(row.current_residence_status);
		            $('#tour-form input[name="current_residence_status_etc"]').attr("style",'display: inline-block!important;');
                }

                if($.inArray(row.current_residence_type, residence_type) >= 0) {
                    $('#tour-form select[name="current_residence_type"]').val(row.current_residence_type);
                }else if (row.current_residence_type) {
                    $('#tour-form select[name="current_residence_type"]').val('기타');
                    $('#tour-form input[name="current_residence_type_etc"]').val(row.current_residence_type);
		            $('#tour-form input[name="current_residence_type_etc"]').attr("style",'display: inline-block!important;');
                }
                $('#tour-form select[name="age"]').val(row.age);
                $('#tour-form input:radio[name="gender"][value="'+row.gender+'"]').prop('checked', true).button('refresh');
                //$('#tour-form input:radio[name="gender"][value="1"]').attr('checked', 'checked').button('refresh');
                //$('#tour-form > #gender'+row.gender).attr('checked', 'checked');
                $('#tour-form select[name="path"]').val(row.path);
                $('#tour-form input[name="job"]').val(row.job);
                $('#tour-form input[name="etc"]').val(row.etc);
    	    });

            tour_table.on("click", ".state-btn", function (e) {
                var row =  tour_table.row( $(this).parent().parent() ).data();

                $.ajax({
		    	    type: "POST",
		    	    url: '/api/tour_state',
		    	    data: {'tour_id':row.id},
		    	    success: function(data)
		    	    {
                        $("#tbl-state tbody").empty();
                        var html = "";
                        if(data.data.length > 0) {
                            $.each(data.data, function (key, val) {
								var tour_time_data= JSON.parse(val.memo); 

								if(tour_time_data != null && typeof(tour_time_data['tour_date']) !== 'undefined' && typeof(tour_time_data['tour_time']) !== 'undefined'){
									html += "<tr><td>"+TOUR_STATE[val.state]+"</td> <td>"+tour_time_data['tour_date']+"</td><td>"+tour_time_data['tour_time']+"시</td> <td>"+val.created_at+"</td></tr>";
								} else {
	                                html += "<tr><td>"+TOUR_STATE[val.state]+"</td><td></td><td></td><td>"+val.created_at+"</td></tr>";
								}
		    			    });
                        }else {
                            html += "<tr><td colspan='4'>내용없음</td></tr>";
                        }
                        $("#tbl-state tbody").append(html);

    	      	        dialog_state.dialog( "open" );
		    	    }
		    	    ,error:function(e){
		    	        alert('통신 에러.');
		    	    }
		    	});

    	    });

            $( "#tour-form #gender" ).buttonset();
            $( "#tour-apply-form #gender" ).buttonset();

		    $('.dropbox select').change(function() {
		        var id = $(this).attr('id');
		        var txt = $(this).children("option:selected").text()
		        if(txt == "기타") {
		            $("#"+id+"_etc").attr("style",'display: inline-block!important;');
		            $("#"+id+"_etc").focus();
		        }else {
		            $("#"+id+"_etc").attr("style",'display: none!important;');
		        }
		    });

            //tour excel upload form
            $("#btn-upload" ).on( "click", function( event ) {
    	    	event.preventDefault();

                var form = $('#excel-upload')[0]
                var data = new FormData(form);
                var url = '/admins/tours/import';

		    	$.ajax({
		    	    type: "POST",
                    enctype: 'multipart/form-data',
		    	    url: url,
		    	    data: data,
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    cache: false,
                    timeout: 600000,
		    	    success: function(data)
		    	    {
                        //console.log(data);
                        if(data.code == true) {
		    	            alert('저장 완료. ');
                            tour_table.ajax.reload( null, false );
		    	        }else {
		    	            alert(data.msg);
                            return false;
		    	        }
		    	    }
		    	    ,error:function(e){
		    	        alert('저장 실패.');
		    	    }
		    	});
		    });
			

			// 투어날짜 등록버튼
		    tour_table.on("click", ".tour-date-apply-btn", function (e) {

				var action = 'add';
				sel_row =  tour_table.row( $(this).parent().parent() ).data();
				
				$('#tour_id').val(sel_row.id);
				$('#tour_state').val(sel_row.state);
				if(sel_row.tour_date){
					action = 'update';
					$('#apply_tour_date').val(sel_row.tour_date);
				}
				if(sel_row.tour_time){
					$('#apply_tour_time').val(sel_row.tour_time);
				}
				$('#reserv_action').val(action);

    	      	tour_date_dialog.dialog( "open");
    	    });

            //tour excel download 
            $("#btn-download" ).on( "click", function( event ) {
                location.href = '/admins/tours/export';
		    });
        } // tour page end

        // notiec apply page start
        if($('#notice-apply-page').length){ 
            var notice_dialog_apply, add_form, add_dialog, modify_form, modify_dialog, house_add_form,house_add_dialog, house_modify_dialog,house_modify_form;
            var notice_apply_table = $('#noticeApplyTable').DataTable({
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
		    	"scrollX": true,
		    	//"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                'ajax': {
                  'url':'/api/notice_apply'
                },
		    	'columns': [
      	    		{ data: 'id' },
                    { data: 'h_name_kr'},
      	    		{ data: 'name',
                        render: function(data, type, row) {
		    				return "<span>"+data+"</span><button class='modify-btn' data-id='"+row.id+"'>수정</button>";	
		    			}
                    },
                    { data: 'state',
		    			render: function(data, type, row) {
                            var $select = $("<select data-id='"+row.id+"' class='state-select'><option value='0'>신규신청</option><option value='1'>메일 발송</option><option value='2'>전화연락</option></select>");
                            $select.find('option[value="'+data+'"]').attr('selected', 'selected');
                            var $div = $("<div></div>");
                            $div.append($select[0].outerHTML);
                            return $div[0].outerHTML
		    			}
		    		},
      	    		{ data: 'phone' },
      	    		{ data: 'email' },
     	    		{ data: 'created_at' },
      	    		{ data: 'etc' }
      	    	],
                columnDefs: [
			        {orderable: false, targets :[1]}
		        ],
                initComplete: function () {
		        	this.api().columns().every( function (e) {
		        		if(e==1  ){
		        			var column = this;
		        			var col_name = '지점';
		        			var select = $('<select><option value="">'+col_name+'</option></select>')
		        			.appendTo( $(column.header()).empty() )
		        			.on( 'change', function () {
		        				var val = $.fn.dataTable.util.escapeRegex(
		        					$(this).val()
		        				);
		        				column.search( val, true, false ).draw();
		        			} );

		        			column.data().unique().sort().each( function ( d, j ) {
		        				select.append( '<option value="'+d+'">'+d+'</option>' )
		        			} );
		        		}
		        	} );
		        },
                "order": [[0, 'desc']]
            });

            //생성 팝업
            add_dialog = $( "#add-dialog" ).dialog({
    	      autoOpen: false,
    	      height: 500,
    	      width: 400,
    	      modal: true,
    	      buttons: {
    	        "save": function(){
		    		add_form.submit();
		    	},
    	        Cancel: function() {
    	          add_dialog.dialog( "close" );
    	        }
    	      },
    	      close: function() {
                //console.log('close');
    	        add_form[ 0 ].reset();
    	        //allFields.removeClass( "ui-state-error" );
    	      }
    	    });

            //수정팝업
		    modify_dialog = $( "#modify-dialog" ).dialog({
    	      autoOpen: false,
    	      height: 300,
    	      width: 400,
    	      modal: true,
    	      buttons: {
    	        "save": function(){
		    		//form.submit();
                    dialog_action();
		    	},
                "delete": function(){
                    dialog_action('remove');
		    	},
    	        Cancel: function() {
    	          modify_dialog.dialog( "close" );
    	        }
    	      },
    	      close: function() {
    	        modify_form[ 0 ].reset();
    	        //allFields.removeClass( "ui-state-error" );
    	      }
    	    });

 
    	    add_form = $("#add-form" ).on( "submit", function( event ) {
    	    	//event.preventDefault();

		    	var $t, t, chk=true;
		    	$(this).find("input, select").each(function(i) {
            	    $t = $(this);

            	    if($t.prop("required")) {
            	        if(!jQuery.trim($t.val())) {
            	            t = jQuery("label[for='"+$t.attr("id")+"']").text();
            	            chk = false;
            	            $t.focus();
            	            alert(t+" 필수 입력입니다.");
            	            return false;
            	        }
            	    }
            	});

		    	if(!chk) {
		    		return false;
		    	}

                var url = '/api/notice_apply/apply';

		    	$.ajax({
		    	    type: "POST",
		    	    url: url,
		    	    data: add_form.serialize(), // serializes the form's elements.
		    	    success: function(data)
		    	    {
                    //console.log(data);
		    	        if(data.code == true) {
		    	            alert('저장 완료. ');
                            notice_apply_table.ajax.reload( null, false );
    	          		    add_dialog.dialog( "close" );
		    	        }else {
		    	            alert(data.msg);
                            return false;
		    	        }
		    	    }
		    	    ,error:function(e){
		    	        alert('저장 실패.');
		    	    }
		    	});
                return false;
		    });
                    
    	    modify_form = modify_dialog.find( "form" );

            function dialog_action (action="update") {

		    	var url = '/api/notice_apply/'+action;

		    	$.ajax({
		    	    type: "POST",
		    	    url: url,
		    	    data: modify_form.serialize(), // serializes the form's elements.
		    	    success: function(data)
		    	    {
		    	        if(data == true) {
		    	            alert('저장 완료. ');
    	          		    modify_dialog.dialog( "close" );
                            notice_apply_table.ajax.reload( null, false );
		    	        }else if(data == -1) {
		    	            alert('저장 실패. 관리자에 문의하세요.');
		    	        }
		    	    }
		    	    ,complete:function(){
    	          		modify_dialog.dialog( "close" );
		    	    }
		    	    ,error:function(e){
		    	        alert('저장 실패.');
		    	    }
		    	});

    	    };

            notice_apply_table.on("change", ".state-select", function (e) {
                //console.log($(this).data('id'));
                var id = $(this).data('id');
                var state = $("option:selected", this).val();
                //var row =  tour_table.row( $(this).parent().parent() ).data();

                $.ajax({
		    	    type: "POST",
		    	    url: '/api/notice_apply/change_state',
		    	    data: {'id':id, 'state':state},
		    	    success: function(data)
		    	    {
                        if(data.success) {
                            alert('수정되어있습니다.');
                        }else {
                            alert('수정 실패.');
                        }
		    	    }
		    	    ,error:function(e){
                        alert('통신 에러.');
		    	    }
		    	});

    	    });

            $('#notice-apply-btn').on("click", function (e) {
    	      	add_dialog.dialog( "open" );
    	    });

		    notice_apply_table.on("click", ".modify-btn", function (e) {
    	      	modify_dialog.dialog( "open" );
                //console.log($(this).data('id'));
                var row =  notice_apply_table.row( $(this).parent().parent() ).data();

                $('#modify-form > #id').val(row.id);
    	    });

        } // notice apply page end
		

		// 지점관리 신규등록 팝업
		if($('#house-apply-page').length){

			//생성 팝업
			house_add_dialog = $( "#house-add-dialog" ).dialog({
			  autoOpen: false,
			  height: 700,
			  width: 400,
			  modal: true,
    	      buttons: {
    	        "save": function(){
		    		house_add_form.submit();
		    	},
    	        Cancel: function() {
    	          house_add_dialog.dialog( "close" );
    	        }
    	      },
    	      close: function() {
    	        house_add_form[0].reset();
 
    	      }
			});
			
			// 등록 폼
			house_add_form = house_add_dialog.find( "form" );

			//수정팝업
		    house_modify_dialog = $( "#house-modify-dialog" ).dialog({
			
			  //테스트 함수
    	      autoOpen: false,
    	      height: 700,
    	      width: 400,
    	      modal: true,
    	      buttons: {
    	        "save": function(){
		    		//form.submit();
                    house_dialog_action();
		    	},
                "delete": function(){
                    house_dialog_action('remove');
		    	},
    	        Cancel: function(){
    				house_modify_dialog.dialog( "close" );
    	        }
    	      },
    	      close: function() {
    	        house_modify_form[0].reset();
    	      }
    	    });
			

			// 지점관리 - 신규등록 클릭 이벤트
			$('#add-house-btn').on("click", function(e) {
				house_add_dialog.dialog( "open" );
			});
			
			// 지점관리 - 수정/삭제 이벤트
			$('.modify-house-btn').on("click", function(e) {
				var key_id = $(this).data('id');

				var url = '/admins/Houses/search_one';
				$.ajax({
					type: "POST",
					url: url,
					data: 'key_id='+key_id, 
					success: function(data)
					{
						if(data.data.id > 0) {
							$('#p_id').val(data.data.id);
							$('#p_admin_id').val(data.data.admin_id);
							$('#p_h_name').val(data.data.name_kr);
							$('#p_e_name').val(data.data.name_en);
							$('#p_flagship').val(data.data.flagship);
							$('#p_airbnb').val(data.data.airbnb);
							$('#p_kakao').val(data.data.kakao);
							$('#p_info_addr').val(data.data.info_addr);
							$('#p_address').val(data.data.address);
							$('#p_type').val(data.data.type);
							$('#p_status').val(data.data.status);

						} else {
							alert(data.msg);
							return false;
						}

						//location.reload();
					}
					,error:function(e){
						alert('저장 실패.');
					}
				});

				house_modify_dialog.dialog( "open" );
			});
			
			
			// 지점등록
    	    house_add_form = $("#house-add-form" ).on( "submit", function( event ) {

		    	var $t, t, chk=true;
		    	$(this).find("input, select").each(function(i) {
            	    $t = $(this);

            	    if($t.prop("required")) {
            	        if(!jQuery.trim($t.val())) {
            	            t = jQuery("label[for='"+$t.attr("id")+"']").text();
            	            chk = false;
            	            $t.focus();
            	            alert(t+" 필수 입력입니다.");
            	            return false;
            	        }
            	    }
            	});

		    	if(!chk) {
		    		return false;
		    	}

                var url = '/admins/Houses/save';
		    	$.ajax({
		    	    type: "POST",
		    	    url: url,
		    	    data: house_add_form.serialize(), // serializes the form's elements.
		    	    success: function(data)
		    	    {
		    	        if(data.code > 0) {
		    	            alert('저장 완료.');
    	          		    house_add_dialog.dialog( "close" );
		    	        }else {
		    	            alert(data.msg);
                            return false;
		    	        }

						location.reload();
		    	    }
		    	    ,error:function(e){
		    	        alert('저장 실패.');
		    	    }
		    	});
                return false;
		    });
			
			house_modify_form = house_modify_dialog.find( "form" );

			// 지점관리 수정/삭제
            function house_dialog_action(action="update") {

		    	var url = '/admins/Houses/'+action;

		    	$.ajax({
		    	    type: "POST",
		    	    url: url,
		    	    data: house_modify_form.serialize(), // serializes the form's elements.
		    	    success: function(data)
		    	    {
		    	        if(data == true) {
		    	            alert('저장 완료. ');
							location.reload();
		    	        }else if(data == -1) {
		    	            alert('저장 실패. 관리자에 문의하세요.');
		    	        }
		    	    }
		    	    ,complete:function(){
    	          		house_modify_dialog.dialog( "close" );
		    	    }
		    	    ,error:function(e){
		    	        alert('저장 실패.');
		    	    }
		    	});

    	    };

		}
    });


})(jQuery); // End of use strict

/*
 * Dashboard
*/
if($('#dashboard').length){
	function applyWeeklyHighlight() {

        $('.ui-datepicker-calendar tr').each(function() {

            if ($(this).parent().get(0).tagName == 'TBODY') {
                $(this).mouseover(function() {
                    $(this).find('a').css({
                     'background' : '#ffffcc',
                     'border' : '1px solid #dddddd'
                    });
                    $(this).find('a').removeClass('ui-state-default');
                    $(this).css('background', '#ffffcc');
                });

                $(this).mouseout(function() {
                    $(this).css('background', '#ffffff');
                    $(this).find('a').css('background', '');
                    $(this).find('a').addClass('ui-state-default');
                });
            }

        });
    }

	function init_static(cnt_data){
        var def_type = {
                '0':'투어신청',
                '1':'메일전송',
                '2':'재확인문자',
                '3':'투어예정',
                '4':'투어진행',
                '7':'투어취소',
                '5':'입주논의',
                '6':'계약완료',
                };
        $('#apply-head').empty();
        $('#apply-body').empty();

        var head = '<th>일자</th>';

        $.each(cnt_data, function (key, val) {
            head += "<th>"+key+"</th>";
        });
        $('#apply-head').html(head);

        var body_html = '';
        $.each(TOUR_STATE, function (t_key, title) {
            body_html += '<tr align="center">';
            body_html += '  <th>'+title+'</th>';

            $.each(cnt_data, function (key, val) {
                var variable = val[t_key];
                if(typeof(variable) !== "undefined" && variable !== null && variable !== '') {
                    body_html += "<td>"+variable+"</td>";
                }else {
                    body_html += "<td>0</td>";
                }
            });
            body_html += '</tr>';
        });
        $('#apply-body').html(body_html);

    }

	var startDate;
    var endDate;
    $('#week-picker').datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
        selectWeek:true,
        onSelect: function(dateText, inst) {
            var date = $(this).datepicker('getDate');
            startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() );
            endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 6);
            var dateFormat = 'yy-mm-dd';
            startDate = $.datepicker.formatDate( dateFormat, startDate, inst.settings );
            endDate = $.datepicker.formatDate( dateFormat, endDate, inst.settings );

            $('#week-picker').val(startDate + '~' + endDate);
            setTimeout("applyWeeklyHighlight()", 100);

            var url = '/api/statistics/tour_apply_cnt';

            $.ajax({
                type: "POST",
                url: url,
                data: {'s_date':startDate, 'e_date':endDate}, // serializes the form's elements.
                dataType: 'json',
                success: function(data)
                {
                    init_static(data);
                }
                ,error:function(e){
                    alert('로드 실패.');
                }
            });


        },
        beforeShow : function() {
            setTimeout("applyWeeklyHighlight()", 100);
        }
    });

}

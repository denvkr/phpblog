/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var poemApp = angular.module('poemApp', ['mgcrea.ngStrap', 'mgcrea.ngStrap.select', 'mgcrea.ngStrap.tooltip', 'mgcrea.ngStrap.helpers.parseOptions','ngAnimate','ngRoute'])
.filter('trusted_html', ['$sce', function($sce){
    return function(text) {
        return $sce.trustAsHtml(text);
    };
}])
.config(function($sceProvider) {
  // Completely disable SCE.  For demonstration purposes only!
  // Do not use in new projects.
  $sceProvider.enabled(false);
})
.directive('getbodyelem', function () {
  return {
    link: function (scope, element, attrs) {
      var documentcomplain = document.getElementsByClassName("complain");
      var documentcomplanetext = document.getElementsByClassName("complane-text");
      var documentgetyourcomment = document.getElementsByClassName("get-your-comment clearfix");
      
      if (Number($('.topline-user-wrap').eq(0).attr('dbid'))===-1) {
        //
        angular.element(documentcomplain).remove();
        //
        angular.element(documentgetyourcomment).remove();
      } else {
        //angular.element(documentcomplanetext).attr('dbid',$('.topline-user-wrap').eq(0).attr('dbid'));
        scope.userid=Number($('.topline-user-wrap').eq(0).attr('dbid'));
      }
    }
  }
})
.directive('styler', function () {
    return {
        restrict: 'A',
        require: 'ngModel',
         link: function (scope, element, attrs, ngModelCtrl) {
         	
         		if(attrs.type == 'checkbox')
         		{
         		
         		//	console.log(ngModelCtrl.modelValue,attrs.ngTrueValue.replace(/'/g,""),attrs);
         			if(attrs.value == attrs.ngTrueValue.replace(/'/g,""))
         				element.prop('checked', true);
         			else	
         				element.prop('checked', false);
         				
         				
         		}
	            $(element).styler({
					onSelectClosed: function() {
						ngModelCtrl.$setViewValue(element.val());
	                    scope.$apply();	
					},
					onFormStyled: function() {					
					
		         								
					}
	            });
        }
    };
})
.controller('PoemCommentsController', function($scope, $http, $filter, $modal, $route, $routeParams, $location, $rootScope) {
    //var searchedpoetries=this;
    $scope.page;
    $scope.elements_on_page = 20;
    $scope.users_part_count;
    $scope.feedsortingtitle;
    $scope.users_count;
    $scope.userid;
    $scope.poemid;
    $scope.ajaxurls;
    $scope.delcommentid;
    $scope.getcommentsblockbtn_visibility=false;
    $scope.delbtnname='Пожаловаться';
    $scope.title = 'Подтверждение действия.';
    function MyModalController($scope) {
      $scope.content = '<h4>Вы действительно хотите удалить запись?</h4>';
      $scope.source=0;      
    }
    function MyModalController1($scope) {
      $scope.content = '<h4>Пожаловаться на запись?</h4>';
      $scope.source=1;
    }

    MyModalController.$inject = ['$scope'];
    MyModalController1.$inject = ['$scope'];

    if ($(location).attr('hostname')==='ipoetry.ru' || $(location).attr('hostname')==='www.ipoetry.ru'){
        var myModal = $modal({controller: MyModalController, templateUrl: '/app_dev.php/modal', show: false,container: 'body',scope:$scope});
        var myModal1 = $modal({controller: MyModalController1, templateUrl: '/app_dev.php/modal', show: false,container: 'body',scope:$scope});        
    }
    if ($(location).attr('hostname')==='ipoetry.local'){
        var myModal = $modal({controller: MyModalController, templateUrl: '/standard-edition-master/web/app_dev.php/modal', show: false,container: 'body',scope:$scope});
        var myModal1 = $modal({controller: MyModalController1, templateUrl: '/standard-edition-master/web/app_dev.php/modal', show: false,container: 'body',scope:$scope});        
    }

    //массив комментариев
    $scope.comments = [
    ];
    $scope.plusPage = function()
    {
            $scope.page++;
    };
    //инициализируем параметры scope
    $scope.init = function(userid,poemid,element,ajaxurls)
    {
        console.log(userid+' '+poemid+' '+element+' '+ajaxurls);
        $scope.comments_part_count;
        $scope.comments_count;
        $scope.elements_on_page=element;
        $scope.page=1;
        $scope.ajaxurls=ajaxurls;
        $scope.userid=userid;
        $scope.poemid=poemid;
        $scope.addComments();
    };
    //получение комментариев
    $scope.addComments = function(){
            $scope.users_count=0;
            console.log($scope.ajaxurls+' '+$scope.page+' '+$scope.userid+' '+$scope.poemid+' '+$scope.elements_on_page);
            if ($scope.page == 1)
                    $scope.comments = [];
            if ($scope.page > 1){
                $scope.page=1;
                $scope.comments = [];
            }
                
            $http.post($scope.ajaxurls,{type:"get_poetry_comments_info",user:$scope.userid,poetry:$scope.poemid,'datapart':$scope.page}).success(function(response)
            {
                    if (response.result >= 1)
                    {
                        console.log(response.commentslist);
                        $scope.comments_part_count = response.commentslist.length;
                        //заполняем элементы данными
                        $scope.comments_count=response.commentscount;
                        $scope.comments=response.commentslist;
                        $scope.getcommentsblockbtn_visibility=true;
                        //защита от множественных ajax
                        if ($scope.page==1)
                            $scope.plusPage();
                    }
                    else
                    {
                        console.log('(#getcommentsblockbtn).fadeOut()');
                        $scope.getcommentsblockbtn_visibility=false;
                        //$('#getcommentsblockbtn').fadeOut();
                        // $scope.loginError = response.error;
                    }
            });
    };
    //еще комметарии
    $scope.moreComments = function(){
        console.log($scope.ajaxurls+' '+$scope.page+' '+$scope.userid+' '+$scope.poemid+' '+$scope.elements_on_page);  
        $http.post($scope.ajaxurls,{type:"get_poetry_comments_info",user:$scope.userid,poetry:$scope.poemid,'datapart':$scope.page,}).success(function(response)
        {
                if (response.result >= 1)
                {
                    console.log(response.commentslist);
                    $scope.comments_part_count = response.commentslist.length;
                    //заполняем элементы данными
                    $scope.comments_count=response.commentscount;
                    $scope.comments=$scope.comments.concat(response.commentslist);
                    $scope.getcommentsblockbtn_visibility=true;
                    $scope.plusPage();
                    //$scope.$apply();
                }
                else
                {
                    $scope.getcommentsblockbtn_visibility=false;
                    //$('#getcommentsblockbtn').fadeOut();
                    // $scope.loginError = response.error;
                }
        });
    };
    $scope.Likefunc=function(commentid,option){
        //console.log(option,commentid.currentTarget.getAttribute("commentid"));

        var oReq = new XMLHttpRequest();
        oReq.onreadystatechange = function() {
            if (oReq.readyState == 4 && oReq.status == 200) {
                response=JSON.parse(oReq.response);
                console.log(response.commentlike+' '+response.commentdislike+' sendLike|Dislike done');
                //$scope.like=response.commentlike;
                //$scope.dislike=response.commentdislike;
                if (typeof response.commentlike!== 'undefined' && typeof response.commentdislike!== 'undefined') {
                    for(i=0;i<$scope.comments.length;i++){
                        //console.log($scope.comments[i].ipoetryUserBlogPostId,commentid.currentTarget.getAttribute("commentid"))
                        if ($scope.comments[i].ipoetryUserBlogPostId===Number(commentid.currentTarget.getAttribute("commentid"))){
                            $scope.comments[i].ipoetryBlogPostRatingValueUp=response.commentlike;
                            $scope.comments[i].ipoetryBlogPostRatingValueDown=response.commentdislike;
                        }
                    };
                    $scope.$apply();
                    //console.log($scope.comments[i],$scope.comments.length);
                }
            }
        };
        //шлем доппараметры как json
        //отправляем like к комментарию
        oReq.open("POST", $scope.ajaxurls,true);
        oReq.setRequestHeader("Content-Type", "application/json");
        console.log("login_json="+JSON.stringify({type:"commentlikerequest", poetry:$scope.poemid,user:$scope.userid,commentid:commentid.currentTarget.getAttribute("commentid"),updown:option}));
        oReq.send(JSON.stringify({type:"commentlikerequest", poetry:$scope.poemid,user:$scope.userid,commentid:commentid.currentTarget.getAttribute("commentid"),updown:option}));

    };
    $scope.AddComment=function(){
        var oReq = new XMLHttpRequest();
        oReq.onreadystatechange = function() {
            //console.log(JSON.parse(oReq.responseText));
            if (oReq.readyState == 4 && oReq.status == 200) {
                response=JSON.parse(oReq.response);
                if (response.result===1){
                    oReq.onreadystatechange = function() {
                        //console.log(JSON.parse(oReq.responseText));
                        if (oReq.readyState == 4 && oReq.status == 200) {
                            response=JSON.parse(oReq.response);
                            console.log(response);
                            $scope.comments=$scope.comments.concat(response.commentslist);
                            $scope.$apply();
                        }
                    }
                    //шлем доппараметры как json
                    //отправляем доп инфо по стихотворению
                    oReq.open("POST", $scope.ajaxurls,true);
                    oReq.setRequestHeader("Content-Type", "application/json");
                    console.log("login_json="+JSON.stringify({type:"add_poetry_comment",poetry:$scope.poemid,user:$scope.userid}));
                    oReq.send(JSON.stringify({type:"add_poetry_comment",poetry:$scope.poemid,user:$scope.userid}));                    
                }
            }
        };
        //console.log($('#UserPoetryCreation_poetry').val());
        //шлем blob тест комментария
        oReq.open("POST", $scope.ajaxurls, true);
        var blob = new Blob([$('#CommentText').val()], {type: 'text/plain'});
        console.log(blob);
        oReq.setRequestHeader("Content-Type", "text/plain");
        oReq.send(blob);
        //return 1;
    };
    
    $scope.complainelement = function(element) {
        console.log($scope.ajaxurls+' '+$scope.page+' '+$scope.userid+' '+element+' '+$scope.elements_on_page);
        $http.post($scope.ajaxurls,{type:"complain_user_post",user:$scope.userid,poetry:element}).success(function(response)
        {
            if (response.result)
            {
                console.log(response.result);
            }
        });
        
    };

    $scope.showModal = function(element) {
          $scope.delcommentid=element.currentTarget.getAttribute("commentid");
      //element.currentTarget.getAttribute("userid");
      if (element.currentTarget.getAttribute('value')==='Пожаловаться'){
        console.log('showModal',$scope.delpoetryid,element.currentTarget.getAttribute("commentid"),myModal);
        myModal1.$promise.then(myModal1.show);
      }
    };
    $scope.hideModal = function(status,element,ajaxurl) {
      console.log('hideModal',element.currentTarget.getAttribute("value"),$scope.delpoetryid,status,ajaxurl);
      $scope.ajaxurls=ajaxurl;
      if (status===1 && Number(element.currentTarget.getAttribute("value"))===1)
        $scope.complainelement($scope.delcommentid);
    };

    //функция наблюдения
    $scope.$watch(function($scope){

    },true);

});
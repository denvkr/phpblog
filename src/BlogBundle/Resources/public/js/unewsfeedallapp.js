/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var unewsfeedallApp = angular.module('unewsfeedallApp', ['mgcrea.ngStrap','ngAnimate','ngRoute'])
.filter('trusted_html', ['$sce', function($sce){
    return function(text) {
        return $sce.trustAsHtml(text);
    };
}])
.filter('moment', function () {
  return function (input, momentFn /*, param1, param2, ...param n */) {
    moment.locale('ru');
    var args = Array.prototype.slice.call(arguments, 2),
        momentObj = moment(input);
    return momentObj[momentFn].apply(momentObj, args);
  };
})
.directive('getbodyelem', function () {
  return {
    link: function (scope, element, attrs) {
      var documentmainpostmoredetails = document.getElementsByClassName("main-post-more-details");
      var documentbtnwhiteborderdel = document.getElementsByClassName("btn-white-border del");
      
      if (Number($('.topline-user-wrap').eq(0).attr('dbid'))===-1) {
        angular.element(documentmainpostmoredetails).remove();
      } else {
        //angular.element(documentmainpostmoredetails).attr('dbid',$('.topline-user-wrap').eq(0).attr('dbid'));
        angular.element(documentbtnwhiteborderdel).attr('dbid',$('.topline-user-wrap').eq(0).attr('dbid'));
        scope.userid=Number($('.topline-user-wrap').eq(0).attr('dbid'));
      }
    }
  }
})
.config(function($sceProvider) {
  // Completely disable SCE.  For demonstration purposes only!
  // Do not use in new projects.
  $sceProvider.enabled(false);
})
.controller('unewsfeedallController', function($scope, $http, $filter, $modal, $route, $routeParams, $location, $rootScope) {
    //var searchedpoetries=this;
    $scope.$route = $route;
    $scope.$location = $location;
    $scope.$routeParams = $routeParams;
    $scope.page;
    $scope.elements_on_page = 20;
    $scope.users_part_count;
    $scope.feedsortingtitle;
    $scope.users_count;
    $scope.userid;
    $scope.poemid;
    $scope.ajaxurls;
    $scope.requesturl;
    $scope.reverse = false;
    $scope.propertyName = 'created';
    $scope.filterset=false;
    $scope.filtersetperiod=365;
    $scope.ownershow=true;
    $scope.MyModal;
    $scope.delpoetryid;
    $scope.delbtnvisibility=0;
    $scope.recommended=false;
    //$modal.delpoetryid;
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
  
    $scope.delbtnname='Удалить';
    //карточки ленты стихов
    $scope.unewsfeed = [
    ];
    $scope.plusPage = function()
    {
            $scope.page++;
    };
    //инициализируем параметры scope
    $scope.init = function(element,ajaxurls,requesturl)
    {
        console.log(element+' '+ajaxurls);
        $scope.unewsfeed_part_count;
        $scope.unewsfeed_count;
        $scope.unewsfeed_on_page=element;
        $scope.page=1;
        $scope.ajaxurls=ajaxurls;
        //$scope.userid=userid;
        $scope.requesturl=requesturl;
        //$scope.modal=modal;
        $scope.addunewsfeed();
    };
    //получение ленты стихов
    $scope.addunewsfeed = function(){
            $scope.users_count=0;
            console.log($scope.ajaxurls+' '+$scope.page+' '+' '+$scope.elements_on_page);
            if ($scope.page === 1)
                    $scope.comments = [];
            if ($scope.page > 1){
                $scope.page=1;
                $scope.comments = [];
            }

            $http.post($scope.ajaxurls,{type:"get_all_newsfeed",user:'all','datapart':$scope.page}).success(function(response)
            {
                    if (response.result >= 1)
                    {
                        console.log(response.unewsfeedlist);
                        $scope.unewsfeed_part_count = response.unewsfeedlistcnt;
                        //заполняем элементы данными
                        $scope.unewsfeed_count=response.unewsfeedcount;
                        $scope.unewsfeed=response.unewsfeedlist;
                        for(i=0;i<$scope.unewsfeed.length;i++){
                          //console.log('deleted',$scope.delpoetryid);
                          if (Boolean($scope.unewsfeed[i].recommended)===true){
                              $scope.unewsfeed[i].recommendvisibility='block';
                          } else {
                              $scope.unewsfeed[i].recommendvisibility='none';                              
                          }
                        };
                        //защита от множественных ajax
                        if ($scope.page===1)
                            $scope.plusPage();
                        //if ($scope.frominit===false){
                        $scope.sortBy($scope.propertyName,$scope.filtersetperiod,$scope.reverse);
                        //}
                    }
                    else
                    {
                        $('#getunewsfeedblockbtn').fadeOut();
                        // $scope.loginError = response.error;
                    }
            });
    };
    //еще комметарии
    $scope.moreuNewsfeed = function(){
        console.log($scope.ajaxurls+' '+$scope.page+' '+$scope.poemid+' '+$scope.elements_on_page);
        $http.post($scope.ajaxurls,{type:"get_all_newsfeed",user:'all','datapart':$scope.page}).success(function(response)
        {
                if (response.result >= 1)
                {
                    console.log(response.unewsfeedlist);
                    $scope.unewsfeed_part_count = response.unewsfeedlistcnt;
                    //заполняем элементы данными
                    $scope.unewsfeed_count=response.unewsfeedcount;
                    for(i=0;i<response.unewsfeedlist.length;i++){
                      //console.log('deleted',$scope.delpoetryid);
                      if (Boolean(response.unewsfeedlist[i].recommended)===true){
                          response.unewsfeedlist[i].recommendvisibility='block';
                      } else {
                          response.unewsfeedlist[i].recommendvisibility='none';                              
                      }
                    };
                    $scope.unewsfeed=$scope.unewsfeed.concat(response.unewsfeedlist);
                    $scope.plusPage();
                    //if ($scope.filterset===false)
                    $scope.sortBy($scope.propertyName,$scope.filtersetperiod,$scope.reverse);
                    //if (response.ownprofile===0)
                    //    $scope.ownershow=false;
                    //$scope.$apply();
                }
                else
                {
                    $('#getunewsfeedblockbtn').fadeOut();
                    // $scope.loginError = response.error;
                }
        });
    };
    $scope.shbutton = function(element) {
        console.log(element.currentTarget.children[0].children[0].getAttribute('dbid'),element.currentTarget.children[0].children[0].getAttribute('userid'));
        if (element.currentTarget.children[0].getAttribute("style")==='display: none; opacity: 1;'){
            element.currentTarget.children[0].setAttribute("style",'display: block; opacity: 1;');
            //меняем кнопку удалить на пожаловаться      
            if (element.currentTarget.children[0].children[0].getAttribute('dbid')===element.currentTarget.children[0].children[0].getAttribute('userid')){
               //element.currentTarget.children[0].children[0].setAttribute("value",'Удалить'); 
               $scope.delbtnname='Удалить';
            } else {
               $scope.delbtnname='Пожаловаться';
               //element.currentTarget.children[0].children[0].setAttribute("value",'Пожаловаться');              
               element.currentTarget.children[0].children[0].setAttribute("style",'width: 140px;padding-left: 15px;');
            }
        }
        //element.currentTarget.children[0].//.stop().fadeToggle();
    };
    $scope.delbutton = function(element) {
        if (element.currentTarget.children[0].getAttribute("style")==='display: block; opacity: 1;')
        element.currentTarget.children[0].setAttribute("style",'display: none; opacity: 1;');
    };
    $scope.delelement = function(element) {
        console.log($scope.ajaxurls+' '+$scope.page+' '+$scope.userid+' '+element+' '+$scope.elements_on_page);
        //return false;
        $http.post($scope.ajaxurls,{type:"del_user_post",user:$scope.userid,poetry:element,source:'NEWSFEED'}).success(function(response)
        {
            if (response.result)
            {
                // To refresh the page
                //$timeout(function () {
                // 0 ms delay to reload the page.
                console.log(response.result);

                $scope.page=response.result.page;
                $scope.elements_on_page=response.result.elements_on_page;
                $scope.users_part_count=response.result.users_part_count;
                $scope.feedsortingtitle=response.result.feedsortingtitle;
                $scope.users_count=response.result.users_count;
                $scope.userid=response.result.userid;
                $scope.poemid=response.result.poemid;
                $scope.ajaxurls=response.result.ajaxurls;
                $scope.requesturl=response.result.requesturl;
                $scope.reverse=response.result.reverse;
                $scope.propertyName=response.result.propertyName;
                $scope.filterset=response.result.filterset;
                $scope.filtersetperiod=response.result.filtersetperiod;
                $scope.ownershow=response.result.ownershow;
                $scope.delpoetryid=response.result.delpoetryid;
                $scope.delbtnvisibility=response.result.delbtnvisibility;
                $scope.title=response.result.title;
                $scope.content=response.result.content;
                $scope.delbtnname=response.result.delbtnname;
                $scope.unewsfeed=response.result.unewsfeed;

                //удаляем элемент из ленты
                unewsfeedlen=$scope.unewsfeed.length;
                for(i=0;i<$scope.unewsfeed.length;i++){
                  if (Number($scope.unewsfeed[i].poetry_id)===Number($scope.delpoetryid)){
                      console.log('deleted',$scope.delpoetryid);                      
                      $scope.unewsfeed.splice(i, 1);
                      //delete $scope.unewsfeed[i];
                      break;
                  };
                };
                //$scope.unewsfeed.length=unewsfeedlen-1
                console.log($scope.unewsfeed);
                //$scope.$apply();
                //$scope.sortBy($scope.propertyName,$scope.filtersetperiod,$scope.reverse);
                //$route.reload();
                //}, 0);
                //$state.transitionTo($state.current, $state.$current.params, { reload: true, inherit: true, notify: true });
                //return true;
                //заполняем элементы данными
                /*
                $scope.unewsfeed_count=($scope.unewsfeed_count-1);
                for(i=0;i<$scope.unewsfeed.length;i++){
                  //console.log(unewsfeeddate,'#',curdate,(unewsfeeddate<curdate));
                  if (Number($scope.unewsfeed[i].poetry_id)===Number(element)){
                      //indx=i;
                      $scope.unewsfeed.splice(i, 1);
                      break;
                  };
                };
                */
                //if (typeof indx !== 'undefined')
                //    $scope.unewsfeed.splice(indx, 1);
                //$scope.$apply(); 
                //if ($scope.filterset===true)
                //    $scope.sortBy($scope.propertyName,$scope.filtersetperiod,false);
            }
        });
        
    };
    $scope.complainelement = function(element) {
        console.log($scope.ajaxurls+' '+$scope.page+' '+$scope.userid+' '+element+' '+$scope.elements_on_page);
        //return false;
        $http.post($scope.ajaxurls,{type:"complain_user_post",user:$scope.userid,poetry:element}).success(function(response)
        {
            if (response.result)
            {
                console.log(response.result);
            }
        });
        
    };

    $scope.sortBy = function(propertyName,period,reverse) {
        if ($scope.filterset===false)
            $scope.reverse=true;
        else
            $scope.reverse=reverse;
      //$scope.reverse = ($scope.propertyName === propertyName) ? !$scope.reverse : false;      
      if (Number($scope.filtersetperiod)<Number(period) && $scope.filterset===true){
         //console.log((Number($scope.filtersetperiod)<Number(period)));          
         $scope.filtersetperiod=period;
          $scope.addunewsfeed();
      };
      $scope.filtersetperiod=period;
      $scope.filterset=true;
      console.log(Number(period));
      if (Number(period)!==365){
        curdate=new Date();
        curdate.setDate(curdate.getDate() - Number(period));
        //console.log(propertyName,period,curdate);
        $scope.unewsfeedtmp = [
        ];
        for(i=0;i<$scope.unewsfeed.length;i++){
          unewsfeeddate=new Date($scope.unewsfeed[i].created);
          console.log(unewsfeeddate,'#',curdate,(unewsfeeddate<curdate));
          if (unewsfeeddate>curdate){
              //indx=i;
              $scope.unewsfeedtmp=$scope.unewsfeedtmp.concat($scope.unewsfeed[i])
          };
        };
        //if (typeof indx !== 'undefined') {
        //    $scope.unewsfeed.splice(indx, ($scope.unewsfeed.length-indx));
        //}
        console.log($scope.unewsfeedtmp);
        $scope.unewsfeed=$scope.unewsfeedtmp;
        if ($('#getunewsfeedblockbtn').length<=$scope.unewsfeed_part_count){
           $('#getunewsfeedblockbtn').fadeOut();          
        };
      };
      //$scope.$apply();
      //$scope.$apply(function() {
      //  $scope.unewsfeed = $scope.unewsfeed;
      //});
      //$scope.$digest();
      $scope.propertyName = propertyName;
    };
    $scope.Likefunc=function(postid,ajaxurl,option){
      //console.log(option,commentid.currentTarget.getAttribute("commentid"));
      //если есть логиненый пользователь то разрешаем лайк
      if (Number($('.topline-user-wrap').eq(0).attr('dbid'))!==-1) {
        var oReq = new XMLHttpRequest();
        oReq.onreadystatechange = function() {
            if (oReq.readyState === 4 && oReq.status === 200) {
                response=JSON.parse(oReq.response);
                console.log(response.poetrylike+' '+response.poetrydislike+' sendLike|Dislike done');
                //$scope.like=response.commentlike;
                //$scope.dislike=response.commentdislike;
                if (typeof response.poetrylike!== 'undefined' && typeof response.poetrydislike!== 'undefined') {
                    for(i=0;i<$scope.unewsfeed.length;i++){
                        //console.log($scope.comments[i].ipoetryUserBlogPostId,commentid.currentTarget.getAttribute("commentid"))
                        if ($scope.unewsfeed[i].poetry_id===Number(postid.currentTarget.getAttribute("poetryid"))){
                            $scope.unewsfeed[i].like=response.poetrylike;
                            $scope.unewsfeed[i].dislike=response.poetrydislike;
                        }
                    };
                    $scope.$apply();
                    //console.log($scope.comments[i],$scope.comments.length);
                }
            }
        };
        
        //шлем доппараметры как json
        //отправляем like к комментарию
        oReq.open("POST", ajaxurl,true);
        oReq.setRequestHeader("Content-Type", "application/json");
        console.log("login_json="+JSON.stringify({type:"poetrylikerequest",poetry:Number(postid.currentTarget.getAttribute("poetryid")),user:$scope.userid,updown:option}));
        oReq.send(JSON.stringify({type:"poetrylikerequest",poetry:Number(postid.currentTarget.getAttribute("poetryid")),user:$scope.userid,updown:option}));          
      }
    };
    $scope.addMessage=function(newmsgurl){
        
        var oReq = new XMLHttpRequest();
        oReq.onreadystatechange = function() {
        if (oReq.readyState === 4 && oReq.status === 200) {
            response=JSON.parse(oReq.response);
            if (response.result===1){
                $http.post($scope.ajaxurls,{type:"new_message",user:$scope.userid}).success(function(response)
                {
                    if (response.result === 1)
                    {
                        $('#poetrymessage').val('');
                        console.log('AJAX_user_new_message result='+response.result);
                        $scope.addunewsfeed();
                    }
                });
            } else
                return 0;
            }
        };

        //посылаем комментарий к стиху
        oReq.open("POST", newmsgurl, true);
        var blob = new Blob([$('#poetrymessage').val()], {type: 'text/plain'});
        oReq.setRequestHeader("Content-Type", "text/plain");
        oReq.send(blob);        

    }
    //function MyModalController($scope) {
    //  $scope.title = 'Подтверждение действия.';
    //  $scope.content = 'Hello Modal<br />This is a multiline message from a controller!';
    //}
    //MyModalController.$inject = ['$scope'];
    $scope.showModal = function(element) {
      $scope.delpoetryid=element.currentTarget.getAttribute("postid");
      $scope.userid=element.currentTarget.getAttribute("userid");
      //$scope.MyModal = $modal({controller: 'unewsfeedallController', templateUrl: '/standard-edition-master/web/app_dev.php/modal/'+$scope.userid+'_'+$scope.delpoetryid, show: false,container: 'body',delpoetryid:$scope.delpoetryid});
      //$scope.MyModal = $modal({controller: 'unewsfeedallController', templateUrl: '/standard-edition-master/web/modal/modaldemo.html.twig?userid='+$scope.userid+'&poetryid='+$scope.delpoetryid, show: false,container: 'body',delpoetryid:$scope.delpoetryid});
      var scopeobj={'page':$scope.page,
                    'elements_on_page':$scope.elements_on_page,
                    'users_part_count':$scope.users_part_count,
                    'feedsortingtitle':$scope.feedsortingtitle,
                    'users_count':$scope.users_count,
                    'userid':$scope.userid,
                    'poemid':$scope.poemid,
                    'ajaxurls':$scope.ajaxurls,
                    'requesturl':$scope.requesturl,
                    'reverse':$scope.reverse,
                    'propertyName':$scope.propertyName,
                    'filterset':$scope.filterset,
                    'filtersetperiod':$scope.filtersetperiod,
                    'ownershow':$scope.ownershow,
                    'delpoetryid':$scope.delpoetryid,
                    'delbtnvisibility':$scope.delbtnvisibility,
                    'title':$scope.title,
                    'content':$scope.content,
                    'delbtnname':$scope.delbtnname,
                    'unewsfeed':$scope.unewsfeed};
      
      if (element.currentTarget.getAttribute('value')==='Удалить'){
        console.log('showModal',$scope.delpoetryid,element.currentTarget.getAttribute("postid"),myModal);
        //производим сохранение на сервер пространства scope для контроллера
        $http.post($scope.ajaxurls,{type:"del_feed_element",scope:scopeobj}).success(function(response)
        {
          if (response.result === 1)
          {
              console.log('AJAX_del_feed_element result='+response.result);
              myModal.$promise.then(myModal.show);
          }
        });

        //var oReq = new XMLHttpRequest();
        //oReq.onreadystatechange = function() {
        //    if (oReq.readyState === 4 && oReq.status === 200) {
        //        response=JSON.parse(oReq.response);
        //        console.log(response.result);
        //    }
        //};

        //шлем scope как json
        //oReq.open("POST", $scope.ajaxurls,true);
        //oReq.setRequestHeader("Content-Type", "application/json");
        //console.log("login_json="+JSON.stringify({type:"del_wall_element",scope:scopeobj}));
        //oReq.send(JSON.stringify({type:"del_wall_element",scope:scopeobj}));

        //$scope.MyModal.$promise.then($scope.MyModal.show);          
      }
      if (element.currentTarget.getAttribute('value')==='Пожаловаться'){
        console.log('showModal',$scope.delpoetryid,element.currentTarget.getAttribute("postid"),myModal);
        //производим сохранение на сервер пространства scope для контроллера
        $http.post($scope.ajaxurls,{type:"del_feed_element",scope:scopeobj}).success(function(response)
        {
          if (response.result === 1)
          {
              console.log('AJAX_del_feed_element result='+response.result);
              myModal1.$promise.then(myModal1.show);
          }
        });

        //var oReq = new XMLHttpRequest();
        //oReq.onreadystatechange = function() {
        //    if (oReq.readyState === 4 && oReq.status === 200) {
        //        response=JSON.parse(oReq.response);
        //        console.log(response.result);
        //    }
        //};

        //шлем scope как json
        //oReq.open("POST", $scope.ajaxurls,true);
        //oReq.setRequestHeader("Content-Type", "application/json");
        //console.log("login_json="+JSON.stringify({type:"del_wall_element",scope:scopeobj}));
        //oReq.send(JSON.stringify({type:"del_wall_element",scope:scopeobj}));

        //$scope.MyModal.$promise.then($scope.MyModal.show);          
      }

    };
    $scope.hideModal = function(status,element,ajaxurl) {
      console.log('hideModal',element.currentTarget.getAttribute("value"),$scope.delpoetryid,status,ajaxurl);
      //$scope.userid=element.currentTarget.getAttribute("userid");
      $scope.ajaxurls=ajaxurl;
      myModal.$promise.then(myModal.hide);
      if (status===1 && Number(element.currentTarget.getAttribute("value"))===0)
        $scope.delelement($scope.delpoetryid);
      if (status===1 && Number(element.currentTarget.getAttribute("value"))===1)
        $scope.complainelement($scope.delpoetryid);

      //MyModal.$promise.then(MyModal.hide);
    };
    //$scope.hideModal = function() {
      //console.log('hideModal',$scope.delpoetryid,status,element.currentTarget.getAttribute("poetryid"),element.currentTarget.getAttribute("userid"),ajaxurl);
      //$scope.userid=element.currentTarget.getAttribute("userid");
      //$scope.ajaxurls=ajaxurl;
      //if (status===1)
      //$scope.delelement($scope.delpoetryid);
      //MyModal.$promise.then(MyModal.hide);
    //};
    //функция наблюдения
    $scope.$watchCollection('unewsfeed', function(newValues, oldValues) {
        //console.log('unewsfeed changed',newValues, oldValues);
    },true);
    $scope.$watchCollection('unewsfeed.recommended', function(newValues, oldValues) {
        //console.log('unewsfeed changed',newValues, oldValues);
    },true);
});


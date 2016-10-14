/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var usersApp = angular.module('usersApp', ['mgcrea.ngStrap.select', 'mgcrea.ngStrap.tooltip', 'mgcrea.ngStrap.helpers.parseOptions','ngAnimate']).filter('trusted_html', ['$sce', function($sce){
        return function(text) {
            return $sce.trustAsHtml(text);
        };
    }]);
usersApp.config(function($sceProvider) {
  // Completely disable SCE.  For demonstration purposes only!
  // Do not use in new projects.
  $sceProvider.enabled(false);
});
/*
usersApp.directive('selectcitybtn', function () {
    return {
         link: function (element) {
            element.trigger('click');
        }
    };
});
*/
usersApp.controller('UsersController', function($scope,$http,$timeout) {
    //var searchedpoetries=this;
    $scope.page;
    $scope.elements_on_page = 20;
    $scope.users_part_count;
    $scope.feedsortingtitle;
    $scope.users_count;
    $scope.usertype;
    $scope.userid;
    $scope.ajaxurls;
    $scope.userid;
    $scope.urltype;
    $scope.feedsortingitemurltype;
    $scope.activestate_s;
    $scope.activestate_f;
    $scope.ownuser;
    $scope.userssearch=[];
    $scope.userssearch_maxlength=100;
    $scope.usersusersubscribersdel=false;
    $scope.usersuserfollowdel=false;
    //карточки подписчиков
    $scope.users = [
    ];
    //города для фильтра
    $scope.selectedbootstrap_cities;
    $scope.cities = {
        availableOptions: [{value:null,label:null}]        
    };
    $scope.bootstrap_cities = {
        selected:null,
        availableOptions: []
    };

    /*
    $scope.searchedpoetries.addPoetrySearch = function() {
      $scope.searchedpoetries.push({p:$scope.searchedpoetriestext.text});
      $scope.searchedpoetriestext.text = '';
    };
    */
    $scope.plusPage = function()
    {
            $scope.page++;
    };

    $scope.init = function(userid,urltype,element,ajaxurls,usertype)
    {/*
        if ($scope.usertype=='subscribers')
            $scope.feedsortingtitle="Вы подписаны на <span>0</span> автора:";
        if ($scope.usertype=='follow')
            $scope.feedsortingtitle="У вас <span>0</span> подписчик:";
        */
        $scope.elements_on_page=element;
        $scope.page=1;
        $scope.ajaxurls=ajaxurls;
        $scope.userid=userid;
        $scope.urltype=urltype;
        if ($scope.urltype.toUpperCase()=='S'){
            $scope.activestate_s='active';
            $scope.activestate_f='';
            $scope.feedsortingitemurltype='S';
            $scope.usertype='subscribers';
            $scope.usersusersubscribersdel=true;
            $scope.usersuserfollowdel=false;            
        }
        if ($scope.urltype.toUpperCase()=='F'){
            $scope.activestate_s='';
            $scope.activestate_f='active';
            $scope.feedsortingitemurltype='F';
            $scope.usertype='follow';
            $scope.usersusersubscribersdel=false;
            $scope.usersuserfollowdel=true;
        }
    
        $scope.ownuser=0;
        //$scope.addCities();
        $scope.addUsers(null,null,$scope.urltype);
    };

    $scope.Users=function(usertype){
        $scope.usertype=usertype;
        if ($scope.usertype.toUpperCase()=='SUBSCRIBERS'){
            $scope.feedsortingitemurltype='S';
            $scope.urltype=$scope.feedsortingitemurltype;
            $scope.usersusersubscribersdel=true;
            $scope.usersuserfollowdel=false;
            $scope.addUsers(null,null,$scope.feedsortingitemurltype);
        }
        if ($scope.usertype.toUpperCase()=='FOLLOW'){
            $scope.feedsortingitemurltype='F';
            $scope.urltype=$scope.feedsortingitemurltype;
            $scope.usersusersubscribersdel=false;
            $scope.usersuserfollowdel=true;
            $scope.addUsers(null,null,$scope.feedsortingitemurltype);
        }
    };
/*
    $scope.LoadPoetries = function(){
            if ($scope.page == 1)
                    $scope.searchedpoetries = [];	
            $http.post('/system/ajax/comments.php',{'action':'Load','filter':$scope.filter.comments,'page':$scope.page}).success(function(response)
            {
                    //$scope.comments = response.comments;
                    if(angular.isArray(response.comments))
                    {
                            $scope.comments_count = response.comments.length;
                            $scope.comments = $scope.comments.concat(response.comments);
                            $scope.comment_authors = $scope.comment_authors.concat(response.authors);
                    }
                    else
                            $scope.comments_count = 0;

                    if($scope.comments_count == 0 || $scope.FindInArray($scope.user.id,$scope.comment_authors))
                            $scope.show_comment_form = true;
                    else
                            $scope.show_comment_form = false;

            });

    }
*/
    $scope.clickOnselectcitybtn = function (event) {    
        $timeout(function() {
            angular.element(document.querySelector("#selectcitybtn")).trigger('focus');
        }, 0);
    };
    $scope.addCities = function(cityfilter,event){
            config={cache:true};
            $http.post($scope.ajaxurls,{type:"get_cities",cityfilter:cityfilter},config).success(function(response)
            {
                if (response.result == 1)
                {
                    $scope.cities.availableOptions=[];
                    $scope.bootstrap_cities.availableOptions=[];
                    //если собственная страница пользователя сессии то делаем кнопки "убрать из подписчиков" неактивными
                    console.log(response.cities);
                    $scope.cities.availableOptions = $scope.cities.availableOptions.concat(response.cities);
                    $scope.bootstrap_cities.availableOptions=$scope.bootstrap_cities.availableOptions.concat(response.cities_bootstrap);
                    //$('#citysearchinput').removeAttr('disabled');

                    $scope.clickOnselectcitybtn(event);
                }
            });
    };
    $scope.CityFilter = function(){
        console.log($scope.bootstrap_cities.selected);
        $scope.addUsers($scope.bootstrap_cities.selected.label,$scope.userssearch,$scope.urltype);
    };
    //ввод данных для поиска по городу
    $scope.clickCityFilter = function(event){
        console.log('1',event,$('#citysearchinput').val());
        $scope.addCities($('#citysearchinput').val(),event);
        if ($('#citysearchinput').val()==0) {
            $scope.addUsers(null,$scope.userssearch,$scope.urltype);         
        }   
        //$('#citysearchinput').attr('disabled','disabled');
    }
    $scope.addUsers = function(city,user,urltype){
            $scope.users_count=0;
            console.log($scope.ajaxurls+' '+$scope.page+' '+$scope.usertype+' '+$scope.elements_on_page+' '+urltype);
            if ($scope.page == 1)
                    $scope.users = [];
            if ($scope.page > 1){
                $scope.page=1;
                $scope.users = [];
            }
                
            $http.post($scope.ajaxurls,{type:"get_users",user:$scope.userid,urltype:urltype,usertype:$scope.usertype,'datapart':$scope.page,'cityfilter':city,'userfilter':user}).success(function(response)
            {
                    if (response.result == 1)
                    {
                        //если собственная страница пользователя сессии то делаем кнопки "убрать из подписчиков" неактивными
                        console.log(response.userslist+' '+response.ownuser);
                        $scope.users_part_count = response.userslist.length;
                        //заполняем элементы данными
                        $scope.users_count=response.userscount;
                        $scope.users=response.userslist;
                        if ($scope.urltype.toUpperCase()=='A'){
                            $scope.usersusersubscribersdel=false;
                            $scope.usersuserfollowdel=false
                        }
                        //защита от множественных ajax
                        if ($scope.page==1)
                            $scope.plusPage();
                        //console.log($scope.users);
                        $scope.ownuser=response.ownuser;
                    }
                    else
                    {
                        $('#getusersblockbtn').fadeOut();
                        // $scope.loginError = response.error;
                    }
            });
    };
    $scope.moreUsers = function(city,user,urltype){
            console.log($scope.ajaxurls+' '+$scope.page+' '+$scope.usertype+' '+$scope.elements_on_page);
            $http.post($scope.ajaxurls,{type:"get_users",user:$scope.userid,urltype:urltype,usertype:$scope.usertype,'datapart':$scope.page,'cityfilter':city,'userfilter':user}).success(function(response)
            {
                    if (response.result == 1)
                    {
                        //если собственная страница пользователя сессии то делаем кнопки "убрать из подписчиков" неактивными
                        console.log(response.userslist+' '+response.ownuser);
                        $scope.users_part_count = response.userslist.length;
                        //заполняем элементы данными
                        $scope.users_count=response.userscount;
                        $scope.users=$scope.users.concat(response.userslist);
                        $scope.plusPage();
                        console.log($scope.users);
                        $scope.ownuser=response.ownuser;
                    }
                    else
                    {
                        $('#getusersblockbtn').fadeOut();
                        // $scope.loginError = response.error;
                    }
            });
    };
    $scope.Unsubscribe = function(usertype,userid){
            $scope.users_count=0;
            console.log($scope.ajaxurls+' '+$scope.page+' '+$scope.usertype+' '+$scope.elements_on_page);
            if ($scope.page == 1)
                    $scope.users = [];
            if ($scope.page > 1){
                $scope.page=1;
                $scope.users = [];
            }

            console.log($scope.ajaxurls+' '+$scope.page+' '+$scope.usertype,userid.currentTarget.getAttribute("userid"),usertype);
            //$scope.users=$filter('filter')($scope.users, {userId: '!16'});
            if (null!==$scope.bootstrap_cities.selected)
                city=$scope.bootstrap_cities.selected.label
            else
                city=null;
            if ($scope.userssearch.length<=$scope.userssearch_maxlength)
                user=$scope.userssearch;
            else
                user=null;
            if ($scope.urltype.toUpperCase()=='A')
                urltype=$scope.urltype;
            else
                urltype=$scope.feedsortingitemurltype;
            $http.post($scope.ajaxurls,{type:"unsubscribe",user:$scope.userid,unsubscribeuser:userid.currentTarget.getAttribute("userid"),usertype:usertype,urltype:urltype,'datapart':$scope.page,'cityfilter':city,'userfilter':user}).success(function(response)
            {
                    if (response.result == 1)
                    {
                        console.log(response.result);
                        $scope.users_part_count = response.userslist.length;
                        //заполняем элементы данными
                        $scope.users=response.userslist;
                        console.log($scope.users);
                    }
            });
    };
    $scope.change_userssearch=function(){
        //console.log($scope.userssearch.length,$scope.userssearch);
        //шлем ajax на сервер для применения фильтра по ФИО пользователя
        if ($scope.userssearch.length<=$scope.userssearch_maxlength){
            if (null!==$scope.bootstrap_cities.selected) {
                if ($scope.urltype.toUpperCase()=='A')
                    $scope.addUsers($scope.bootstrap_cities.selected.label,$scope.userssearch,$scope.urltype);
                else
                    $scope.addUsers($scope.bootstrap_cities.selected.label,$scope.userssearch,$scope.feedsortingitemurltype);
            } else {
                if ($scope.urltype.toUpperCase()=='A')
                    $scope.addUsers(null,$scope.userssearch,$scope.urltype);
                else
                    $scope.addUsers(null,$scope.userssearch,$scope.feedsortingitemurltype);
            }
        }

        //console.log('$scope.userssearch wrong length');        
    };
    $scope.gotolink=function (userid) {
        console.log('../subscribers/'+userid.currentTarget.getAttribute('userid')+'_s');
        location.href='../subscribers/'+userid.currentTarget.getAttribute('userid')+'_s';
    };
    $scope.$watch(function($scope){
        if ($scope.ownuser==1){
            /*
            $('button[class="users-user-subscribers-del"]').each(function(index){
              //console.log($(this));
              $(this).fadeIn();
            if ($scope.usertype=='subscribers'){
               $('button[class="users-user-subscribers-del"]:eq(0)').fadeOut();
               $('button[class="users-user-subscribers-del"]:eq(1)').fadeIn();             
            }
            if ($scope.usertype=='follow'){
               $('button[class="users-user-subscribers-del"]:eq(0)').fadeIn();
               $('button[class="users-user-subscribers-del"]:eq(1)').fadeOut(); 
            }
            });
            */
            if ($scope.usertype=='subscribers')
                $scope.feedsortingtitle="подписан(а) на <span>"+$scope.users_count+"</span> человек(а):";
            if ($scope.usertype=='follow')
                $scope.feedsortingtitle="<span>"+$scope.users_count+"</span> человек(а) в подписчиках:";
        } else {
            /*
            $('button[class="users-user-subscribers-del"]').each(function(index){
              //console.log($(this));
              $(this).fadeOut();
            });
            */
            if ($scope.usertype=='subscribers')
                $scope.feedsortingtitle="подписан(а) на <span>"+$scope.users_count+"</span> человек(а):";
            if ($scope.usertype=='follow')
                $scope.feedsortingtitle="<span>"+$scope.users_count+"</span> человек(а) в подписчиках:";
        }
    },true);

});

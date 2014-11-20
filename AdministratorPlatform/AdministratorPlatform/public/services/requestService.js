/**
 * created by Harbon
 * date:2014-10-26
 */

AdministratorPlatform.factory('requestService', ['$http', '$rootScope', '$location', function ($http, $rootScope, $location) {
    var domain = "http://www.flappyant.com/book/API.php/"

    var currentTab = null;
    $rootScope.administrator = {
        userId:null,
        password:null
    }
//    登陆
    var login = function ($scope) {
        console.log('求不黑，谢谢合作！！：）')
        var userName = $scope.userName;
        var password = $scope.password;
        var loginMessage = {
            userId:userName,
            password:password
        }

        $http({
            method:'POST',
            url:domain+'administrator/login',
            data:loginMessage,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function (successInfo) {
            if (successInfo.status == 1) {
            $rootScope.navShouldShow = true;
            $rootScope.administrator = loginMessage;
            $location.path('/booksList');
        }else{
                $.UIkit.notify('用户名密码错误', 'info')
            }
        }).error(function (errorInfo) {
            console.log('error');
            console.log(errorInfo);
        });
    }
//    身份验证
    var validate = function () {
        if (!$rootScope.administrator.userId) {
            $location.path('/login')
        }
    }
//分页初始化
    var paginationInit = function (tab, $scope) {
        currentTab = $scope;
        var url = '';
        if (tab == 0) {
            url = 'bookSum/1/0';
        }else{
            url = 'bookSum/1/1';
        }
        $http({
            method:'GET',
            url:domain+url
        }).success(function (result) {
            $scope.paginationShouldShow = true
            $scope.book_number = result.sum;
            var totalPages = parseInt(result.sum) / 15 + 1;
            var pages = [];
            for (var i = 1; i <= totalPages; i++) {
                pages.push(i);
            }
            $scope.pages = pages;
            if (tab == 0) {
                booksListRequest(1, $scope);
            }else{
                bookInLendingRequest(1, $scope);
            }

        }).error(function (result) {
            console.log(result);
        })
    }
//    全部图书列表请求
    var booksListRequest = function (page, $scope) {

        $http({
            url:domain+'searchA/12108238/5/page='+page+'/all',
            method:'GET'
        }).success(function (books) {
//            获取数据操作
            for (var i = 0;i<books.length;i++) {
                books[i].barCodeInfoUrl = 'http://qr.liantu.com/api.php?text='+barCodeInfoAnalyse(books[i]);
            }
            $scope.books = books;
//            页面状态更新
            $scope.currentPage = page;

            if ($scope.currentPage == 1) {
                $scope.notFirstPage = false;
            }else {
                $scope.notFirstPage = true;
            }

            if (parseInt($scope.currentPage) == $scope.pages.length){
                $scope.notLastPage = false;
            }else {
                $scope.notLastPage = true;
            }

        }).error(function (err) {
            $.UIkit.notify(err+'', 'error')
        })

    }
//    解析书本生成二维码内容
    var barCodeInfoAnalyse = function (book) {
        var content = '{book_name:'+book.book_name+',book_Info:'+book.book_Info+',book_id:'+book.book_id+'}';
        return content;
    }
//    图书添加
    var bookAdd = function ($scope) {
        var newBook = {
            bookName:$scope.book_name,
            bookAuthor:$scope.book_author,
            actId:0,
            bookType:$scope.book_type,
            bookInfo:$scope.book_info,
            bookPrice:$scope.book_price,
            bookPic:$scope.book_pic
        }
        $http({
            method:'POST',
            url:domain+'administrator/addBook/'+$rootScope.administrator.userId+'/'+$rootScope.administrator.password,
            data:newBook,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function (result) {
            $.UIkit.notify('添加成功', 'success')
        }).error(function (result) {

        })
    }
//    图书更新
    var bookUpdate = function (index, $scope) {
        var updateInfo = {
            bookName:$scope.books[index].book_name,
            bookAuthor:$scope.books[index].book_author,
            bookPic:$scope.books[index].book_pic,
            bookType:$scope.books[index].book_type,
            bookInfo:$scope.books[index].book_info,
            bookStatus:$scope.books[index].book_status
        };
        $http({
            url:domain+'booklist/book/update/'+$rootScope.administrator.userId+'/'+$scope.books[index].book_id+'/'+$rootScope.administrator.password,
            method:'POST',
            data:updateInfo,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function (result) {
            $.UIkit.notify('更新成功', 'success')
            console.log('update successfully')
        }).error(function (result) {
            console.log('update failure');
        })
    }
//    图书删除
    var bookDelete = function (index, $scope) {
        $http({
            method:'GET',
            url:domain+'administrator/deleteBook/'+$scope.books[index].book_id+'/'+$rootScope.administrator.userId+'/'+$rootScope.administrator.password
        }).success(function (result) {
            $scope.books.splice(index, 1)
            $.UIkit.notify('删除成功', 'success')
            console.log('delete successfully');
        }).error(function (result) {
            console.log('delete failure');
        })
    }
//    已借出图书列表请求
    var bookInLendingRequest = function (page, $scope) {
       $http({
           url:domain+'administrator/return/'+$rootScope.administrator.userId+'/'+$rootScope.administrator.password+'/page='+page,
           method:'GET'
       }).success(function (books) {
           $scope.books = books;
           $scope.currentPage = page;

           if ($scope.currentPage == 1) {
               $scope.notFirstPage = false;
           }else {
               $scope.notFirstPage = true;
           }

           if (parseInt($scope.currentPage) == $scope.pages.length){
               $scope.notLastPage = false;
           }else {
               $scope.notLastPage = true;
           }
       }).error(function (result) {
           $.UIkit.notify(result+'', 'error')
       })
    }
//    查询
    var search = function ($scope) {
       var category = $scope.category;
        var keyword = $scope.keyword;

        $http({
            method:'GET',
            url:domain+'searchA/'+$rootScope.administrator.userId+'/'+category+'/page=not/'+keyword
        }).success(function (books) {
            currentTab.books = books;
            currentTab.paginationShouldShow = false
        }).error(function (error) {
            console.log('error search');
        })
    }

//  登出
    var logout = function ($scope) {
        $rootScope.administrator = null;
        $location.path('/login')
    }
    return {
        paginationInit:paginationInit,
        booksListRequest:booksListRequest,
        bookUpdate:bookUpdate,
        bookAdd:bookAdd,
        bookDelete:bookDelete,
        login:login,
        booksInLendingRequest:bookInLendingRequest,
        search:search,
        validate:validate,
        logout:logout
    }
}])
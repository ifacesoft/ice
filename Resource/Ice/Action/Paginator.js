var Paginator = {
    page: function (page, action, params) {
        params.page = page;

        console.log(params);

        Ice.call(
            action,
            params,
            function (result) {
                Ice.reRender(result)
            }
        );
    }
};


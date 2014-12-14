var Paginator = {
    page: function (page, action, params) {
        params.page = page;

        Ice.reRender(action, params);
    }
};


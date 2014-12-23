var Ice_Paginator = {
    page: function ($element, page, action, params) {
        params.page = page;

        Ice.reRenderClosest($element, action, params);
    }
};


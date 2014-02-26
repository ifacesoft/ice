var Account = {
    switchTab: function () {
        $('div.tab-content div').each(
            function () {
                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                } else {
                    $(this).addClass('active');
                }
            }
        );

        $('ul.nav-tabs li').each(
            function () {
                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                } else {
                    $(this).addClass('active');
                }
            }
        );
    },

    /**
     * Обработка авторизации пользователя
     */
    login: function ($form) {
        Ice.call(
            'ice\\action\\Account_Login',
            Ice.getFormData($form),
            function (result) {
                Account.clearMessages();
                if (result.data.error) {
                    var error = result.data.error;
                    Account.addErrorMsg($form, error.message);
                } else {
                    Account.addNoticeMsg($form, 'Авторизация успешно продена. Добро пожаловать!');
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                }
            }
        );
    },

    /**
     * Обработка регистрации пользователя
     */
    register: function ($form) {
        Ice.call(
            'ice\\action\\Account_Register',
            Ice.getFormData($form),
            function (result) {
                Account.clearMessages();

                if (result.data.error) {
                    var error = result.data.error;
                    Account.addErrorMsg($form, error.message);
                } else {
                    Account.addNoticeMsg($form, 'Регистрация прошла успешно!');
                    setTimeout(function () {
                        Account.switchTab();
                    }, 1000);
                }
            }
        );
    },

    /**
     * Добавить сообщение об ошибке на форму.
     * Указав в elementId несколько идентификаторов элементов
     * через запятую рамка добавится ко все указанным элементам,
     * а сообщение будет отображено для самого последнего элемента набора.
     */
    addErrorMsg: function ($form, message) {
        $("<div>",
            {
                class: 'alert alert-danger',
                text: message
            })
            .css(
            {
                textAlign: 'center'
            })
            .appendTo($(".Account_form"));
    },

    addNoticeMsg: function ($form, message) {
        $("<div>",
            {
                class: 'alert alert-success',
                text: message
            })
            .css(
            {
                textAlign: 'center'
            })
            .appendTo($(".Account_form"));
    },

    clearMessages: function () {
        $('.Account .alert').remove();
    },

    beforeOpen: function () {
        $('.Account :input.clean').val('');
    },

    logout: function () {
        Ice.call(
            'Account_Logout',
            {},
            function (result) {
                if (result) {
                    location.href = result.data.redirect
                        ? result.data.redirect
                        : "/";
                }
            }
        );
    }
};


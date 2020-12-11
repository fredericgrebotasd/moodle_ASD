define(['jquery', 'core/notification', 'core/ajax'],
    function ($,
              notification,
              ajax
    ) {
        return {
            init: function () {
                $(document).ready(function () {
                    $('.ajaxcall').click(function (e) {
                            var element = $(this);
                            e.stopPropagation();
                            e.preventDefault();
                            var promises = ajax.call([{
                                methodname: 'local_ibob_hello_world',
                                args: {welcomemessage: "Hellow World!"},
                                done: console.log("ajax done"),
                                fail: notification.exception
                            }]);
                            promises[0].then(function (data) {
                                console.log(data); //Data contains webservice answer.
                                element.replaceWith(data);
                            });
                        }
                    );
                });
            }
        };
    });
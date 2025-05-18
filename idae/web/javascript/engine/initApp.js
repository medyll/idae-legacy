initApp = function () {

    new selfObservers('body');

    Ajax.currentRequests = {};
    Ajax.Responders.register({
        onCreate: function (request) {
            if (Ajax.currentRequests['monitor']) {
                try {
                    Ajax.currentRequests['monitor'].transport.abort();
                } catch (e) {
                }
            }
            if (request.options.onlyLatestOfClass && Ajax.currentRequests[request.options.onlyLatestOfClass]) {
                try {
                    Ajax.currentRequests[request.options.onlyLatestOfClass].transport.abort();
                } catch (e) {
                }
            }
            Ajax.currentRequests[request.options.onlyLatestOfClass] = request;
        },
        onComplete: function (request, transport) {
            if (request.container.success) {
                if ($(request.container.success)) {
                    afterAjaxCall($(request.container.success));
                    $(request.container.success).fire('content:loaded');
                }
            }
            if (request.options.onlyLatestOfClass) {
                Ajax.currentRequests[request.options.onlyLatestOfClass] = null;
            }
        }
    });

}

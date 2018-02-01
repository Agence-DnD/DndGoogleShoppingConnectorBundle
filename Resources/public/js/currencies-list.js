'use strict';


define([
    'jquery',
    'underscore',
    'oro/translator',
    'pim/fetcher-registry',
    'pim/job/common/edit/field/select',
    'routing'
], function (
    $,
    _,
    __,
    FetcherRegistry,
    SelectField,
    Routing
) {
    return SelectField.extend({
        /**
         * {@inherit}
         */
        configure: function () {
            return $.when(
                FetcherRegistry.getFetcher('currencies-list').fetchAll(),
                SelectField.prototype.configure.apply(this, arguments)
            ).then(function (currenciesList) {
                if (_.isEmpty(currenciesList)) {
                    this.config.readOnly = true;
                    this.config.options = {'NO OPTION': __('dnd_google_shopping.google_currency_reader.currency.no_currency')};
                } else {
                    this.config.options = currenciesList;
                }
            }.bind(this));
        }
    });
});
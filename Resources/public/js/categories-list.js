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
                FetcherRegistry.getFetcher('categories-list').fetchAll(),
                SelectField.prototype.configure.apply(this, arguments)
            ).then(function (categoriesList) {
                if (_.isEmpty(categoriesList)) {
                    this.config.readOnly = true;
                    this.config.options = {'NO OPTION': __('dnd_google_shopping.google_category_reader.category.no_category')};
                } else {
                    this.config.options = categoriesList;
                }
            }.bind(this));
        }
    });
});
/**
 * Created by wangchao on 6/7/17.
 */

define(["vue"],function (Vue) {
    Vue.filter('reverse', function (value) {
        return value.split('').reverse().join('')
    });
    Vue.filter('unixToDate', function (timestamp, format) {
        if (!format) {
            format = 'YYYY-MM-DD HH:mm:ss';
        }
        return moment.unix(new Number(timestamp)).format(format);
    });
    Vue.filter('currencyDisplay', {
        // model -> view
        // formats the value when updating the input element.
        read: function(val, currencyCode) {
            if (!currencyCode) {
                currencyCode = '¥ ';
            }
            val = new Number(val);
            return currencyCode+val.toFixed(2)
        },
        // view -> model
        // formats the value when writing to the data.
        write: function(val, oldVal) {
            var number = +val.replace(/[^\d.]/g, '')
            return isNaN(number) ? 0 : parseFloat(number.toFixed(2))
        }
    });
    Vue.filter('currencyUnitDisplay', function (currencyCode) {
        if (currencyCode == "CNY") {
            return "元";
        }
        return currencyCode;
    });
    return "vue-filters";
});
/*!
 * https://github.com/wusfen/cnIDCardNumberValidation
 */
/*
refer:
http://baike.baidu.com/link?url=71WUNChZmbegzlvx6ltfJ-5bN_PC5gNU4mttf2xkmS0ua8_vlnDRvFdtGdUJK2p7S-BONoFsbQ-hf9WhvXNxR8KEDfH4ddHLLRmDfp5kKlRXtCDBkBg7uC24tMFI9w_MrLs8QWCflM7SgUdLRgR7NfDHHHvEs2TlerpqI3D4jl6O9nDxIKmGwkq-jiSS6qXleQoJ_qtYCuXETYvN66rwwO-LLFd8C_iGV-CfTKGc4aNVUtavZvp0bJlfnKdh9F1V
*/


define(
    function(){
        var map = {
            // 省
            '1-2': {
                // 华北地区
                11: '北京市',
                12: '天津市',
                13: '河北省',
                14: '山西省',
                15: '内蒙古自治区',
                // 东北地区
                21: '辽宁省',
                22: '吉林省',
                23: '黑龙江省',
                // 华东地区
                31: '上海市',
                32: '江苏省',
                33: '浙江省',
                34: '安徽省',
                35: '福建省',
                36: '江西省',
                37: '山东省',
                // 华中地区
                41: '河南省',
                42: '湖北省',
                43: '湖南省',
                // 华南地区
                44: '广东省',
                45: '广西壮族自治区',
                46: '海南省',
                // 西南地区
                51: '四川省',
                52: '贵州省',
                53: '云南省',
                54: '西藏自治区',
                50: '重庆市',
                // 西北地区
                61: '陕西省',
                62: '甘肃省',
                63: '青海省',
                64: '宁夏回族自治区',
                65: '新疆维吾尔自治区',
                // 特别地区：
                71: '台湾地区', //(886)
                81: '香港特别行政区', //(852)
                82: '澳门特别行政区', //(853)
            },
            // 市
            '3-4': 0,
            // 区
            '5-6': 0,
            // 生日
            '7-14': function(dateStr) {
                dateStr = dateStr.replace(/(\d{4}|\d{2})(?=\d)/g, '$1-');
                var date = new Date(dateStr);
                var x = new Date('x') + '';
                return date == x ? null : dateStr;
            },
            // 顺序码 奇数男偶数女
            '15-17': function(str3) {
                return (+str3) % 2;
            },
            // 校验码
            /*
             1、将前面的身份证号码17位数分别乘以不同的系数。从第一位到第十七位的系数分别为：7－9－10－5－8－4－2－1－6－3－7－9－10－5－8－4－2。
             2、将这17位数字和系数相乘的结果相加。
             3、用加出来和除以11，看余数是多少？
             4、余数只可能有0－1－2－3－4－5－6－7－8－9－10这11个数字。其分别对应的最后一位身份证的号码为1－0－X －9－8－7－6－5－4－3－2。(即馀数0对应1，馀数1对应0，馀数2对应X...)
             5、通过上面得知如果余数是3，就会在身份证的第18位数字上出现的是9。如果对应的数字是2，身份证的最后一位号码就是罗马数字x。
             */
            '18': function(idStr) {
                // 1
                var x = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
                // 2
                var sum = 0;
                for (var i = 0; i < 17; i++) {
                    sum += idStr.substr(i, 1) * x[i];
                }
                // 3
                var mod = sum % 11;
                // 4
                var mx = [1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2];
                // 5
                return mx[mod] == idStr.substr(17).toUpperCase();
            }
        }
        /**
         * 身份证号码校验
         * @param  {String} idStr - 身份证号码
         * @return {false|Object} - 合法则返回信息(省，生日，性别)，否则 false
         */
        function validate(idStr) {
            // console.log(idStr.substring(7-1, 14));
            //

            // 省
            var province = map['1-2'][idStr.substr(0, 2)];
            if (!province) {
                return false
            };
            // 市

            // 区

            // 生日
            var date = map['7-14'](idStr.substr(6, 8));
            if (!date) {
                return false
            };
            // 顺序码 奇数男偶数女
            var sex = map['15-17'](idStr.substr(14, 3));

            // 校验码
            if (!map['18'](idStr)) {
                return false
            };

            return {
                province: province,
                date: date,
                sex: sex == 1 ? '男' : '女'
            };
        }

        return validate;
    }
);
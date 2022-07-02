/**
 * @author 张娇
 * @date 2022/6/7
 * @description 前端检查输入是否合法
 * @description 在前端引入此js文件，在需要检查的输入框标签内，设置value属性（初始值设为空，即""即可），
 * 通过onblur事件（或者其他事件），即可将输入值传递进指定方法。
 * 例：<input name="password"  type="password" class="form-control" placeholder="密码" value="" onblur="dealPsw(this)"/>
 * */

/**
 * 密码验证：8-16位数字和字母组合，不合条件则弹窗
 * */
function dealPsw(obj) {
    let psw = obj.value;
    let regNumber = /\d+/; //验证0-9的任意数字最少出现1次。
    let regAlphabet = /[a-zA-Z]+/; //验证大小写26个字母任意字母最少出现1次。
    let regOther = /[^a-zA-Z0-9];

    checkLength(obj, 8,16);
    if(!regNumber.test(psw) || !regAlphabet.test(psw)){
        window.alert("密码应为数字和字母组合");
    }else if(regOther.test(psw)){
        window.alert("密码只能为字母和数字的组合");
    }
}

/**
 * 检查电话号码是否合规
 * */
function checkPhone(obj){
    let phoneNum = obj.value;
    let regPhone = /^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|17[0|1|2|3|5|6|7|8]|18[0|1|2|3|5|6|7|8|9])\d{8}$/;
    if(!regPhone.test(phoneNum)) window.alert("您输入的号码不合规");
}

/**
 * 检查email地址是否合规
 * */
function checkEmail(obj){
    let email = obj.value;
    let regEmail = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
    if(!regEmail.test(email)) window.alert("您输入的邮箱不合规");
}

/**
 * 输入长度限制的整数
 * */
function checkNumLength(obj, expectMin, expectMax){
    if(checkDigit(obj)) {
        checkLength(obj, expectMin, expectMax);
    }
}
/**
 * 输入内容长度限制
 * */
function checkMinLength(obj, expectMin){
    checkLength(obj, expectMin,  Number.MAX_VALUE);
}

function checkMaxLength(obj, expectMax){
    checkLength(obj, 0,  expectMax);
}

function checkLength(obj, expectMin, expectMax){
    let actualLength = obj.value.length;
    if(actualLength < expectMin){
        window.alert("长度不能低于"+expectMin);
    }else if(actualLength > expectMax){
        window.alert("长度不能大于"+expectMax);
    }else return true;
}

/**
 * 输入内容只能为整数
 * */
function checkDigit(obj){
    let value = obj.value;
    let regDigit = /^[0-9]*$/;
    if(!regDigit.test(value)){
        window.alert("只能输入整数");
    }else return true;
}


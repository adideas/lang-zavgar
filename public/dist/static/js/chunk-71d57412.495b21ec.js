(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-71d57412"],{1:function(e,t){},10:function(e,t){},11:function(e,t){},12:function(e,t){},13:function(e,t){},14:function(e,t){},15:function(e,t){},2:function(e,t){},"2a0c":function(e,t,s){"use strict";s.r(t);var a=function(){var e=this,t=e.$createElement,s=e._self._c||t;return s("div",{directives:[{name:"loading",rawName:"v-loading",value:e.loading,expression:"loading"}],staticClass:"app-container"},[s("header-sticky",{attrs:{title:"Создание пользователя"}},[s("template",{slot:"prepend"},[s("el-button",{attrs:{type:"success",icon:"el-icon-check"},on:{click:e.createUser}},[e._v("Сохранить")])],1)],2),e._v(" "),s("div",{staticClass:"el-col-24"},[s("div",{staticClass:"el-col-xs-24 el-col-sm-24 el-col-md-24 el-col-lg-12 el-col-xl-12 el-col-12",staticStyle:{"margin-top":"10px"}},[s("el-card",{attrs:{header:"Основные"}},[s("el-form",[s("el-form-item",{attrs:{label:"Имя"}},[s("el-input",{model:{value:e.form.name,callback:function(t){e.$set(e.form,"name",t)},expression:"form.name"}})],1),e._v(" "),s("el-form-item",{attrs:{label:"Email"}},[s("el-input",{model:{value:e.form.email,callback:function(t){e.$set(e.form,"email",t)},expression:"form.email"}})],1),e._v(" "),s("el-form-item",{attrs:{label:"Пароль"}},[s("el-input",{attrs:{type:e.show_password?"text":"password"},model:{value:e.form.password,callback:function(t){e.$set(e.form,"password",t)},expression:"form.password"}},[s("template",{slot:"append"},[s("el-button",{attrs:{icon:"el-icon-view"},on:{click:function(t){e.show_password=!e.show_password}}})],1)],2)],1)],1)],1)],1),e._v(" "),s("div",{staticClass:"el-col-xs-24 el-col-sm-24 el-col-md-24 el-col-lg-11 el-col-lg-offset-1 el-col-xl-11 el-col-xl-offset-1 el-col-12",staticStyle:{"margin-top":"10px"}},[s("el-card",{attrs:{header:"Доступы"}},[s("el-transfer",{staticClass:"my-transfer-user",attrs:{filterable:"",data:e.access_list,titles:["Все доступы","Предоставленные"],props:{key:"id",label:"name"}},model:{value:e.form.access_id,callback:function(t){e.$set(e.form,"access_id",t)},expression:"form.access_id"}})],1)],1)])],1)},l=[],o=s("2865"),n=s.n(o),c=s("0a66"),i={name:"UserCreate",data:function(){return{loading:!0,show_password:!1,access_list:[],form:{email:"",password:"",name:"",access_id:[]}}},created:function(){var e=this;Object(c["b"])("user-access").then((function(t){e.access_list=t.data||[]})).finally((function(t){e.loading=!1})),this.form.password=n.a.generate({length:10,numbers:!0,uppercase:!1,excludeSimilarCharacters:!0})},methods:{createUser:function(){var e=this;Object(c["e"])("user",this.form).then((function(t){e.$router.back()}))}}},r=i,f=(s("6eb5"),s("2877")),u=Object(f["a"])(r,a,l,!1,null,"5e23c64a",null);t["default"]=u.exports},3:function(e,t){},4:function(e,t){},5:function(e,t){},6:function(e,t){},"6eb5":function(e,t,s){"use strict";var a=s("fd2f"),l=s.n(a);l.a},7:function(e,t){},8:function(e,t){},9:function(e,t){},fd2f:function(e,t,s){}}]);
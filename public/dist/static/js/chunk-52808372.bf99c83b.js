(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-52808372"],{a9ca:function(e,s,a){},aa30:function(e,s,a){"use strict";var t=a("a9ca"),o=a.n(t);o.a},f347:function(e,s,a){"use strict";a.r(s);var t=function(){var e=this,s=e.$createElement,a=e._self._c||s;return a("div",{directives:[{name:"loading",rawName:"v-loading",value:e.loading,expression:"loading"}],staticClass:"app-container"},[a("header-sticky",{attrs:{title:"Редактирование пользователя"}},[a("template",{slot:"prepend"},[a("el-button",{attrs:{type:"success",icon:"el-icon-check"},on:{click:e.createUser}},[e._v("Сохранить")])],1)],2),e._v(" "),a("div",{staticClass:"el-col-24"},[a("div",{staticClass:"el-col-xs-24 el-col-sm-24 el-col-md-24 el-col-lg-12 el-col-xl-12 el-col-12",staticStyle:{"margin-top":"10px"}},[a("el-card",{attrs:{header:"Основные"}},[a("el-form",[a("el-form-item",{attrs:{label:"Имя"}},[a("el-input",{model:{value:e.form.name,callback:function(s){e.$set(e.form,"name",s)},expression:"form.name"}})],1),e._v(" "),a("el-form-item",{attrs:{label:"Email"}},[a("el-input",{model:{value:e.form.email,callback:function(s){e.$set(e.form,"email",s)},expression:"form.email"}})],1),e._v(" "),a("el-form-item",{attrs:{label:"Пароль"}},[a("el-input",{attrs:{type:e.show_password?"text":"password"},model:{value:e.form.password,callback:function(s){e.$set(e.form,"password",s)},expression:"form.password"}},[a("template",{slot:"append"},[a("el-button",{attrs:{icon:"el-icon-view"},on:{click:function(s){e.show_password=!e.show_password}}})],1)],2)],1),e._v(" "),a("el-form-item",{attrs:{label:"Старый пароль"}},[a("el-input",{attrs:{type:e.show_password?"text":"password"},model:{value:e.form.confirm_password,callback:function(s){e.$set(e.form,"confirm_password",s)},expression:"form.confirm_password"}},[a("template",{slot:"append"},[a("el-button",{attrs:{icon:"el-icon-view"},on:{click:function(s){e.show_password=!e.show_password}}})],1)],2)],1)],1)],1)],1),e._v(" "),a("div",{staticClass:"el-col-xs-24 el-col-sm-24 el-col-md-24 el-col-lg-11 el-col-lg-offset-1 el-col-xl-11 el-col-xl-offset-1 el-col-12",staticStyle:{"margin-top":"10px"}},[a("el-card",{attrs:{header:"Доступы"}},[a("el-transfer",{staticClass:"my-transfer-user",attrs:{filterable:"",data:e.access_list,titles:["Все доступы","Предоставленные"],props:{key:"id",label:"name"}},model:{value:e.form.access_id,callback:function(s){e.$set(e.form,"access_id",s)},expression:"form.access_id"}})],1)],1)])],1)},o=[],l=a("0a66"),r={name:"UserEdit",data:function(){return{loading:!0,show_password:!1,access_list:[],form:{email:"",password:"",confirm_password:"",name:"",access_id:[]}}},created:function(){var e=this;this.loading=!0,Object(l["b"])("user-access").then((function(s){e.access_list=s.data||[],Object(l["d"])("user",e.$route.params.id).then((function(s){e.form=s.data||{}}))})).finally((function(s){e.loading=!1}))},methods:{createUser:function(){var e=this;Object(l["f"])("user",this.$route.params.id,this.form).then((function(s){e.$router.back()}))}}},c=r,i=(a("aa30"),a("2877")),n=Object(i["a"])(c,t,o,!1,null,"0755fd7a",null);s["default"]=n.exports}}]);
(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-2d2073e1"],{a062:function(t,e,n){"use strict";n.r(e);var a=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{directives:[{name:"loading",rawName:"v-loading",value:t.loading,expression:"loading"}],staticClass:"app-container"},[n("header-sticky",{attrs:{title:"Пользователи"}},[n("template",{slot:"prepend"},[n("el-button",{attrs:{type:"success",icon:"el-icon-plus"},on:{click:function(e){return t.$router.push({name:"UserCreate"})}}},[t._v("Добавить")])],1)],2),t._v(" "),n("el-table",{attrs:{data:t.users.data}},[n("el-table-column",{attrs:{label:"id"},scopedSlots:t._u([{key:"default",fn:function(e){return[n("el-col",[t._v("# "+t._s(e.row.id))])]}}])}),t._v(" "),n("el-table-column",{attrs:{prop:"email",label:"email"}}),t._v(" "),n("el-table-column",{attrs:{prop:"name",label:"name"}}),t._v(" "),n("el-table-column",{attrs:{label:"last time"},scopedSlots:t._u([{key:"default",fn:function(e){return[e.row.last_login_at?n("el-col",[t._v(t._s(t._f("humanDate")(e.row.last_login_at)))]):t._e()]}}])}),t._v(" "),n("el-table-column",{attrs:{align:"right"},scopedSlots:t._u([{key:"default",fn:function(e){return[n("el-button-group",[n("el-button",{attrs:{icon:"el-icon-edit",type:"primary",size:"mini"},on:{click:function(n){return t.$router.push({name:"UserEdit",params:{id:e.row.id}})}}}),t._v(" "),n("el-button",{attrs:{icon:"el-icon-delete",type:"danger",size:"mini"},on:{click:t.removeUser}})],1)]}}])})],1),t._v(" "),n("div",{staticClass:"mt-10"},[n("el-pagination",{staticStyle:{"text-align":"center",position:"fixed",width:"100%",bottom:"15px"},attrs:{"page-sizes":[10,25,50],"page-size":t.query.to,total:t.users.total,background:"",layout:"total, sizes, prev, pager, next","current-page":t.users.current_page},on:{"size-change":function(e){t.query.to=e,t.getList()},"current-change":function(e){t.query.page=e,t.getList()}}})],1)],1)},o=[],i=n("0a66"),r=["января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря"],l=["янв.","фев.","мар.","апр.","мая","июня","июля","авг.","сент.","окт.","нояб.","дек."],c=function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"1900-01-01 01:01:01",e=!(arguments.length>1&&void 0!==arguments[1])||arguments[1],n=new Date,a=new Date(t);Date.now(),a.getTime();if(n.toDateString()===a.toDateString())return"Сегодня ".concat(t.slice(11,13),":").concat(t.slice(14,16));if(n.toDateString()===new Date(+a+864e5).toDateString())return"Вчера ".concat(t.slice(11,13),":").concat(t.slice(14,16));if(a.getFullYear()===n.getFullYear())return"".concat(a.getDate()," ").concat(r[a.getMonth()],"  ").concat(t.slice(11,13),":").concat(t.slice(14,16));var o=e?"".concat(t.slice(11,13),":").concat(t.slice(14,16)):"";return"\n        ".concat(a.getDate(),"\n        ").concat(l[a.getMonth()],"\n        ").concat(a.getFullYear()," г.\n        ").concat(o,"\n      ")},s={name:"UserList",filters:{humanDate:c},data:function(){return{loading:!0,users:{},query:{page:1,to:10}}},created:function(){this.getList()},methods:{getList:function(){var t=this;this.loading=!0,Object(i["b"])("user",this.query).then((function(e){t.users=e.data||[]})).finally((function(e){t.loading=!1}))},removeUser:function(){}}},u=s,g=n("2877"),d=Object(g["a"])(u,a,o,!1,null,"2a658bf2",null);e["default"]=d.exports}}]);
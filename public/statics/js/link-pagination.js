/**
 * Created by wangchao on 6/2/17.
 */
define(["pagination-util-class","vue"],function(PaginationUtilClass, Vue){
    var template = (function () {/*
          <nav v-if="totalPageCount" style="text-align: center;">
               <ul class="pagination">
                     <li v-if="pageNum == 1" class="disabled">
                       <a>
                         <span>&laquo;</span>
                       </a>
                     </li>
                     <li v-if="pageNum != 1">
                       <a v-bind:href="paginationUtil.getPrevPageUrl()">
                         <span>&laquo;</span>
                       </a>
                     </li>
                     <template v-for="n in paginationNumberRange">
                         <li v-if="paginationUtil.isNeedDisplayPageNum(n) && ( pageNum == n ) " class="active">
                             <a>{{n}}</a>
                         </li>
                         <li v-if="paginationUtil.isNeedDisplayPageNum(n) && ( pageNum != n ) ">
                             <a v-bind:href="paginationUtil.getPageUrlByPageNum(n)">{{n}}</a>
                         </li>

                         <li v-if="(!paginationUtil.isNeedDisplayPageNum(n)) && (  (2 == n ) || ( (totalPageCount-1) == n ) )">
                             <a>..</a>
                         </li>
                     </template>
                     <li v-if="pageNum != totalPageCount">
                       <a v-bind:href="paginationUtil.getNextPageUrl()">
                         <span>&raquo;</span>
                       </a>
                     </li>
                     <li v-if="pageNum == totalPageCount" class="disabled">
                       <a>
                         <span>&raquo;</span>
                       </a>
                     </li>

                     <li v-if="isShowGoPage" class="go-to-page" style="display: inline-block;width: 120px;">
                        <div class="input-group">
                            <input type="number" class="form-control" min="1" v-model="goPageNum">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" @click="goPage()">去该页</button>
                            </span>
                        </div>
                     </li>

               </ul>
          </nav>
     */}).toString().match(/[^]*\/\*([^]*)\*\/\}$/)[1];


    var LinkPagination = Vue.extend({
        template: template,
        props: {
            totalPageCount: Number,
            pageNum: Number,
            pageNumName: String,
            isShowGoPage: {
                type: Boolean,
                default: true
            }
        },
        data: function() {
            var paginationUtil = new PaginationUtilClass();
            if (this.pageNumName) {
                paginationUtil.pageNumName = this.pageNumName;
            }

            return {
                paginationUtil: paginationUtil,
                paginationNumberRange: [],
                goPageNum: ""
            };
        },
        ready: function () {
            if (isNaN(this.pageNum)) {
                console.log("pageNum is not a number");
                return;
            }
            if (isNaN(this.totalPageCount)) {
                console.log("totalPageCount is not a number");
                return;
            }
            if (this.pageNum <1 || this.pageNum > this.totalPageCount) {
                return;
            }
            var paginationNumberRange= [];
            paginationNumberRange.push(1);
            if (this.totalPageCount > 1) {
                paginationNumberRange.push(2);
            }
            if (this.pageNum > 2) {
                paginationNumberRange.push(this.pageNum-1);
            }
            paginationNumberRange.push(this.pageNum);
            if (this.pageNum < (this.totalPageCount-1)) {
                paginationNumberRange.push(this.pageNum+1);
            }
            if (this.totalPageCount > 1) {
                paginationNumberRange.push(this.totalPageCount-1);
            }
            paginationNumberRange.push(this.totalPageCount);
            paginationNumberRange = _.sortBy(paginationNumberRange);
            this.paginationNumberRange = _.uniq(paginationNumberRange);
            this.paginationUtil.pageNum = this.pageNum;
            this.paginationUtil.totalPageCount = this.totalPageCount;
        },
        methods:{
            goPage:function(){
                var paginationUtil = this.paginationUtil;
                var goPageNum= parseInt(this.goPageNum);
                if (!goPageNum) {
                    return;
                }
                if(goPageNum<=0||goPageNum>this.totalPageCount){
                    return;
                }
                window.location.href = paginationUtil.getPageUrlByPageNum(goPageNum);
            }
        }
    });
    Vue.component('link-pagination', LinkPagination);
    return "link-pagination";
});
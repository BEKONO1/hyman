<template>

    <div class="row">
        <div class="col-12" v-if="!service.package_type">
            <div class="bg-light p-sm-5 p-3 mb-5 booking-detail-service-box rounded-4">
              <div class="row align-items-center">
                <div class="col-lg-3 col-md-4">
                  <div class="img flex-shrink-0">
                    <img :src="service.service_image" class="object-cover rounded-3 w-100 img-fluid book-service-img" alt="service">
                  </div>
                </div>
                <div class="col-lg-9 col-md-8 mt-md-0 mt-3">
                  <div class="content flex-grow-1">
                  <div class="d-sm-flex align-items-center gap-3 justify-content-between">
                    <h4 class="mb-0">{{ service.name }}</h4>
                   <div class="flex-shrink-0 d-inline-flex align-items-center gap-2 mt-sm-0 mt-2">
  <span class="text-primary fw-500 d-inline-block position-relative h5">
    <span v-if="service.price > 0">{{ formatCurrencyVue(service.price) }}</span>
    <span v-else>Free</span>
  </span>

  <template v-if="formattedDuration(service.duration)">
    <span class="font-size-18">/</span>
    <span class="h5 text-body">{{ formattedDuration(service.duration) }}</span>
  </template>
</div>
                  </div>
                  <div class="d-sm-flex gap-2 mt-3">
                    <h6 class="m-0 lh-1">{{ $t('messages.category') }}:</h6>
                    <ul class="list-inline mt-sm-0 mt-2 mb-0 p-0 d-flex align-items-center flex-wrap category-list lh-1">
                      <li>{{ service.category_name }}</li>
                      <li v-if="service.subcategory_name">{{ service.subcategory_name }}</li>
                    </ul>
                  </div>
                  <div class="d-flex align-items-center flex-wrap gap-2 mt-4">
                    <div class="d-inline-flex align-items-center gap-2 felx-shrink-0">
                      <div class="flex-shrink-0">
                        <img :src="service.provider_image" alt="service" class="img-fluid rounded-3 object-cover avatar-24">
                      </div>
                      <a :href="`${baseUrl}/provider-detail/${service.provider_id}`">
                          <span class="font-size-14 service-user-name">{{ service.provider_name }}</span>
                      </a>
                    </div>
                    <div>/</div>
                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                      <span class="text-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" class="service-rating">
                          <path d="M6.58578 0.85525L7.92167 3.44562C8.02009 3.63329 8.20793 3.76362 8.42458 3.79259L11.4252 4.21427C11.6005 4.23802 11.7595 4.32723 11.8669 4.46335C11.9731 4.59773 12.0187 4.76803 11.9929 4.93543C11.9719 5.07445 11.9041 5.20304 11.8003 5.30151L9.62603 7.33523C9.467 7.47714 9.39498 7.68741 9.43339 7.89304L9.96871 10.7522C10.0257 11.0974 9.78867 11.4229 9.43339 11.4884C9.28696 11.511 9.13693 11.4872 9.0049 11.4224L6.32833 10.0768C6.12968 9.98005 5.89503 9.98005 5.69639 10.0768L3.01982 11.4224C2.69094 11.5909 2.28346 11.4762 2.10042 11.1634C2.0326 11.0389 2.0086 10.897 2.0308 10.7585L2.56612 7.89883C2.60453 7.69378 2.53191 7.48236 2.37348 7.34044L0.19921 5.30788C-0.0594455 5.06692 -0.0672472 4.67014 0.181806 4.42048C0.187207 4.41527 0.193209 4.40948 0.19921 4.40369C0.302432 4.30232 0.438061 4.23802 0.584493 4.22123L3.58514 3.79896C3.80118 3.76942 3.98902 3.64025 4.08805 3.45141L5.37592 0.85525C5.49055 0.632821 5.7282 0.494383 5.98625 0.500175H6.06667C6.29052 0.526241 6.48556 0.660046 6.58578 0.85525Z" fill="currentColor"></path>
                        </svg>
                      </span>
                      <h6 class="font-size-14">{{ service.total_rating }}<span class="text-body"> ({{ service.total_reviews }} {{ $t('messages.reviews') }})</span></h6>
                    </div>
                  </div>
                </div>
                </div>
              </div>
            </div>
        </div>
        <div class="col-12" v-else>
            <div class="bg-light p-sm-5 p-3 mb-5 booking-detail-service-box rounded-4">
              <div class="row align-items-center">
                <div class="col-lg-3 col-md-4">
                  <div class="img flex-shrink-0">
                    <img :src="service.package_image" class="object-cover rounded-3 w-100 img-fluid" alt="service">
                  </div>
                </div>
                <div class="col-lg-9 col-md-8 mt-md-0 mt-3">
                  <div class="content flex-grow-1">
                  <div class="d-sm-flex align-items-center gap-3 justify-content-between">
                    <!-- <h4 class="mb-0">{{ service.name }}</h4>
                    <p>{{service.description}}</p> -->
                    
                      <div class="d-inline-flex align-items-sm-center align-items-start flex-sm-row flex-column gap-3">
                        <div class="comment-user-info">
                            <h4 class="mb-0">{{ service.name }}</h4>
                            <span class="">
                              {{service.description}}
                            </span>
                            <div class="mt-4 " v-if="service.end_at !== null">
                              <div class="d-inline-flex">
                                <span>{{$t('messages.Package_Expire')}}: </span>&nbsp;
                                <p class="text-primary commnet-content m-0">
                                  {{ formatDate(service.end_at) }} 
                                </p>
                              </div>
                            </div>
                        </div>
                      </div>
                   
                    <div class="flex-shrink-0 d-inline-flex align-items-center gap-2 mt-sm-0 mt-2">
                      <span class="text-primary fw-500 d-inline-block position-relative font-size-18">{{ formatCurrencyVue(service.price) }} (<del>{{ formatCurrencyVue(service.total_price) }}</del>)</span>
                    </div>
                  </div>
                </div>
                </div>
              </div>
            </div>
        </div>
        <div class="col-12 mt-5">
          <form  @submit="formSubmit">
           <!-- <input type="hidden" name="_token" :value="csrfToken"> -->
            <div class="col-12">
                
                <template v-if="!isChildComponentVisible">
  
                  <div class="row">
                    <div :class="service.price == 0 ? 'col-lg-12' : 'col-lg-7'">
                      <div  class="booking-list-content-active">
                          <h5 class="text-capitalize">{{ $t('messages.schedule_ervice') }}</h5>
          
                          
                          <div v-if="serviceaddon && serviceaddon.length">
                            <h5 class="mt-5 mb-3">{{ $t('landingpage.Add-ons') }}</h5>
                            <div v-for="(service, index) in serviceaddon" :key="index" class="mb-4 pb-4 d-flex align-items-sm-center aling-items-start flex-sm-row flex-column gap-5">
                                <div class="flex-shrink-0 provider-image-container">
                                    <img :src="service.serviceaddon_image" alt="service-image"
                                        class="img-fluid w-100" style="width: 100px; height:100px;">
                                </div>
                                <div>
                                    <h5 class="text-capitalize mb-1">{{ service.name }}</h5>
                                    <h6>{{ formatCurrencyVue(service.price) }}</h6>
                                </div>
                                <div>
                                  <span class="d-block btn btn-light text-danger" @click="removeAddons(index)" >{{ $t('landingpage.remove') }}</span>
                                </div>
                            </div>
                          </div>
          
                          <div class="mt-5 card bg-light rounded-3">
                            <div class="card-body booking-service-form">
                              <div class="row g-3">
      
                                  <div v-if="service.is_slot == 1" class="col-12">
      
                                      <div class="mt-1">
                                        
                                          <div>
                                          
                                              <div class="px-4 pt-3 pb-4 bg-body">
                                                  <div class="select-week-days">
              
                                                      <div class="custom-form-field">
                                                          <label class="form-label">{{ $t('landingpage.date_time') }}</label>
                      
                                                        
                                                          <DatePicker v-model="DateFormate" view="weekly" :attributes="todos" mode="DateFormate" :min-date="new Date()"  @click="handleDateSelect(DateFormate)" expanded/>
                                                          
                                                          <span v-if="errorMessages['date']">
                                                              <ul class="text-danger">
                                                                <li v-for="err in errorMessages['date']" :key="err">{{ err }}</li>
                                                              </ul>
                                                            </span>
                                                            <span class="text-danger">{{ errors.date }}</span>                               
                                                        </div>
                                                      
                                                  </div>
                                                  <div v-if="date==null" class="time-slot mt-3 pt-3 border-top">
                                                      <p class="text-capitalize mb-2 lh-1">{{ $t('landingpage.date_time') }}</p>
                
                                                          <div  v-for="(dayInfo, index) in availableserviceslot" :key="index">
              
                                                              <div v-if="dayInfo.day === dayName">
              
                                                                  <div v-if="dayInfo.slot != null">
              
                                                          
                                                                      <ul class="list-inline m-0 d-flex align-items-center flex-wrap gap-3">
              
                                                                      <li class="time-slot" v-for="timeSlot in dayInfo.slot" :key="timeSlot">
                                                                          <!-- <span class="btn btn-sm time-slot-btn font-size-14">{{ timeSlot }}</span> -->
                                                                          <input type="radio" :id="timeSlot" v-model="start_time" :value="timeSlot" name="start_time" class="btn-check"/>
                                                                          <label :for="timeSlot" class="btn d-block py-2 px-2 time-slot-btn" >
                                                                            {{ (timeSlot && timeSlot.match(/^(\d{2}):\d{2}:\d{2}$/)) ? RegExp.$1 + ":00" : 'Invalid Time' }}
                                                                          </label>
                                                                      </li>
                                                                    </ul>
              
                                                                  </div>
              
                                                                  <div v-else>
                                                          
                                                                      {{ $t('landingpage.slot_not_available') }}
                                                                  </div>
      
                                                                  <span v-if="errorMessages['start_time']">
                                                                      <ul class="text-danger">
                                                                        <li v-for="err in errorMessages['start_time']" :key="err">{{ err }}</li>
                                                                      </ul>
                                                                    </span>
                                                                    <span class="text-danger">{{ errors.start_time }}</span>
              
                                                              </div>
                                                        
                                                        </div>
                                                    
                                                  </div>
                                              </div>
                                          </div>
                              
                                  </div>
      
                                  </div>
                                  
                                  <div v-else class="col-sm-6">
                                      <label class="form-label">{{ $t('landingpage.date_time') }}</label>
                                      <div class="input-group icon-left custom-form-field flex-nowrap">
                                          <span class="input-group-text flex-shrink-0" id="dateandtime">
                                              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                                                  <path d="M1.32031 6.5531H14.6883" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                  <path d="M11.3322 9.4823H11.3392" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                  <path d="M8.00408 9.4823H8.01103" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                  <path d="M4.66815 9.4823H4.67509" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                  <path d="M11.3322 12.3971H11.3392" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                  <path d="M8.00408 12.3971H8.01103" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                  <path d="M4.66815 12.3971H4.67509" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                  <path d="M11.0329 1V3.46809" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                  <path d="M4.97435 1V3.46809" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                  <path fill-rule="evenodd" clip-rule="evenodd" d="M11.1787 2.18433H4.82822C2.6257 2.18433 1.25 3.41128 1.25 5.6666V12.4538C1.25 14.7446 2.6257 15.9999 4.82822 15.9999H11.1718C13.3812 15.9999 14.75 14.7659 14.75 12.5106V5.6666C14.7569 3.41128 13.3882 2.18433 11.1787 2.18433Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                              </svg>
                                          </span>
                                          <flat-pickr
                                                v-model="date"
                                                :config="config"
                                                class="form-control"
                                                placeholder="Select date and time"
                                                name="date" />
                                      </div>
      
                                      <span v-if="errorMessages['date']">
                                          <ul class="text-danger">
                                            <li v-for="err in errorMessages['date']" :key="err">{{ err }}</li>
                                          </ul>
                                        </span>
                                        <span class="text-danger">{{ errors.date }}</span>
                                  </div>
                                
      
                                  <div v-if="service.type=='fixed'" class="col-sm-4" :class="{'mt-4': service.is_slot == 1}">
                                      <label class="form-label">{{ $t('landingpage.quantity') }}</label>
                                      <div class="custom-form-field">
                                          <div class="btn-group iq-qty-btn" data-qty="btn" role="group">
                                              <button type="button" class="iq-quantity-plus" @click="decrement()">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 256 256"><path d="M224,128a8,8,0,0,1-8,8H40a8,8,0,0,1,0-16H216A8,8,0,0,1,224,128Z"/></svg>                                                
                                              </button>
                                              <input type="text" class="input-display" id="quntity" v-model="quantity" name="quantity" disabled />
                                              <button type="button" class="iq-quantity-minus"  @click="increment()">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 256 256"><path d="M224,128a8,8,0,0,1-8,8H136v80a8,8,0,0,1-16,0V136H40a8,8,0,0,1,0-16h80V40a8,8,0,0,1,16,0v80h80A8,8,0,0,1,224,128Z"/></svg>
                                              </button>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="col-12 mt-5">
                                      <label class="form-label">{{ $t('landingpage.location') }}</label>
                                      <div class="input-group icon-left custom-form-field">
                                          <span class="input-group-text align-items-start pt-4">
                                              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="15"
                                                  viewBox="0 0 14 15" fill="none">
                                                  <path fill-rule="evenodd" clip-rule="evenodd"
                                                      d="M8.875 6.37538C8.875 5.33943 8.03557 4.5 7.00038 4.5C5.96443 4.5 5.125 5.33943 5.125 6.37538C5.125 7.41057 5.96443 8.25 7.00038 8.25C8.03557 8.25 8.875 7.41057 8.875 6.37538Z"
                                                      stroke="currentColor" stroke-width="1.5"
                                                      stroke-linecap="round" stroke-linejoin="round" />
                                                  <path fill-rule="evenodd" clip-rule="evenodd"
                                                      d="M6.99963 14.25C6.10078 14.25 1.375 10.4238 1.375 6.42247C1.375 3.28998 3.89283 0.75 6.99963 0.75C10.1064 0.75 12.625 3.28998 12.625 6.42247C12.625 10.4238 7.89849 14.25 6.99963 14.25Z"
                                                      stroke="currentColor" stroke-width="1.5"
                                                      stroke-linecap="round" stroke-linejoin="round" />
                                              </svg>
                                          </span>
                                          <textarea class="form-control"
                                          :placeholder="$t('placeholder.address')" v-model="address" name="address"></textarea>
      
                                          
                                      </div>
                                      <span v-if="errorMessages['address']">
                                          <ul class="text-danger">
                                            <li v-for="err in errorMessages['address']" :key="err">{{ err }}</li>
                                          </ul>
                                      </span>
                                      <span class="text-danger">{{ errors.address }}</span>

                                      <div>
                                        <!-- <a @click="getCurrentLocation" class="btn btn-primary mt-5">Get Current Location</a> -->
                                        <a @click="getCurrentLocation" class="btn btn-primary mt-5">
                                          <span v-if="isLoading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                          <span v-else>{{ $t('landingpage.get_current_location') }}</span>
                                        </a>
                                      </div>
                                  </div>
                              </div>
                            </div>
                          </div>
                      </div>
                    </div>
                    <div class="col-lg-5" v-if="service.price>0">
                      <div class="booking-list-content">
                          <div class="d-flex justify-content-between">
                              <h5 class="m-0 text-capitalize">{{ $t('landingpage.price_detail') }}</h5>
                              <button v-if="SeletedCouponId>0" class="d-block btn btn-sm py-2 btn-danger text-end" @click="RemoveCoupon()" >{{ $t('landingpage.remove_coupon') }}</button>
                          </div>
                          <div  class="table-responsive mt-5">
                              <table class="table">
                                  <tbody>
                                      <tr>
                                          <td class="ps-0"><span class="text-capitalize">{{ $t('messages.Price') }}</span></td>
                                          <td class="pe-0"><span
                                                  class="d-block text-primary text-end">{{formatCurrencyVue(service.price*quantity)}}</span></td>
                                      </tr>
                                      <tr v-if="discount >0">
                                          <td class="ps-0"><span class="text-capitalize">{{ $t('messages.discount') }} ({{service.discount}}% off)</span>
                                          </td>
                                          <td class="pe-0"><span
                                                  class="d-block text-success text-end">-{{formatCurrencyVue(discount)}}</span></td>
                                      </tr>
          
                                      
                                       <tr v-if="validCoupons.length > 0 && SeletedCouponId == 0 && props.service.package_type == null">
  <td class="ps-0">
    <span class="text-capitalize">{{ $t('landingpage.coupon') }}</span>
  </td>
  <td class="pe-0">
    <span class="d-block text-primary text-end cursor-pointer" @click="OpenCouponCardMethod()">
      {{ $t('messages.apply_coupon') }}
    </span>
  </td>
</tr>

<tr v-if="validCoupons.length > 0 && SeletedCouponId > 0 && selectedCoupon != null && props.service.package_type == null">
  <td class="ps-0">
    <span class="text-capitalize cursor-pointer" @click="OpenCouponCardMethod()">
      {{ $t('landingpage.coupon') }} ({{ selectedCoupon.code }})
    </span>
  </td>
  <td class="pe-0">
    <span class="d-block text-success text-end">{{ formatCurrencyVue(coupondiscount) }}</span>
  </td>
</tr>

<tr v-if="OpenCouponCard == 1">
  <td>
   <couponcard
  @getSelectedCoupon="handleCouponResponse"
  :coupons="validCoupons"
  :service_price="service.price"
  :SeletedCouponId="SeletedCouponId"
/>
  </td>
</tr>

                                      <tr v-if="serviceaddon">
                                          <td class="ps-0"><span class="">{{ $t('landingpage.Add-ons') }}</span></td>
                                          <td class="pe-0"><span
                                                  class="d-block text-primary text-end">{{formatCurrencyVue(addonAmount)}}</span></td>
                                      </tr>
          
                                      <tr>
                                          <td class="ps-0"><span class="text-capitalize">{{ $t('landingpage.subtotal') }}</span></td>
                                          <td class="pe-0"><span
                                                  class="d-block text-primary text-end">{{ formatCurrencyVue(subtotal || 0) }}</span></td>
                                      </tr>
          
                                      <tr >
                                          <td class="ps-0"><span class="text-capitalize">{{ $t('landingpage.tax') }}</span></td>
                                          <td class="pe-0"><span
                                                  class="d-block text-danger text-end"><i v-if="taxAmount>0" class="fa fa-info-circle text-body cursor-pointer" aria-hidden="true" @click="openTaxModel()"></i> +{{ formatCurrencyVue(taxAmount||0)}}</span></td>
                                          </tr>
          
          
                                      <tr>
                                          <td class="border-bottom-0 ps-0 pt-3">
                                              <h5 class="m-0 text-capitalize">{{ $t('landingpage.total') }}</h5>
                                          </td>
                                          <td class="border-bottom-0 pe-0 pt-3">
                                              <h5 class="m-0 text-end">{{ formatCurrencyVue(totalAmount)}}</h5>
                                          </td>
                                      </tr>
                                      <tr v-if="service.is_enable_advance_payment == 1">
                                          <td class="border-bottom-0 ps-0 pt-3">
                                              <h5 class="m-0 text-capitalize">{{ $t('messages.advance_payment_amount') }} ({{service.advance_payment_amount}} %)</h5>
                                          </td>
                                          <td class="border-bottom-0 pe-0 pt-3">
                                              <h5 class="m-0 text-end">{{ formatCurrencyVue(advance_payment_amount)}}</h5>
                                          </td>
                                      </tr>
                                     
                                  </tbody>
                              </table>
                          </div>
                          <div class="mt-1 pt-md-1 pt-1 text-md-end">
                              <div class="d-inline-flex align-items-center flex-wrap gap-3">
                                <p class="m-0 text-capitalize">{{ $t('messages.wallet_balance') }}:</p>
                                <p class="m-0 text-end">{{ formatCurrencyVue(wallet_amount)}}</p>
                              </div>
                            </div>
                          <div class="mt-5 pt-md-5 pt-3 text-md-end">
                              <div class="d-inline-flex align-items-center flex-wrap gap-3">
                                {{console.log(service.service_id)}}
                                <a v-if="!service.package_type" :href="`${baseUrl}/service-detail/${service.id}`" class="btn btn-outline-primary">{{ $t('landingpage.cancel') }}</a>
                                <a v-if="service.package_type" :href="`${baseUrl}/service-detail/${service.service_id}`" class="btn btn-outline-primary">{{ $t('landingpage.cancel') }}</a>
                                  
                                  <button  type="submit"  v-if="service.is_enable_advance_payment == 1"  class="btn btn-primary"> <span v-if="IsLoading==1" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span v-else>{{ $t('landingpage.pay_advance') }}</span></button>
                                  <button type="submit"  v-else class="btn btn-primary"> <span v-if="IsLoading==1" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span v-else>{{ $t('messages.book_now') }}</span></button>
                              </div>
                          </div>
                      </div>
                    </div>
                    <div class="col-lg-12 text-end" v-else>
                      <button type="submit"  class="btn btn-primary"> <span v-if="IsLoading==1" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span v-else>{{ $t('messages.book_now') }}</span></button>
                    </div>
                  </div>     
                </template>
                <component :is="currentComponent" :service="service" :booking_id="bookingId" :customer_id="user_id" :discount="discount"  :total_amount="totalAmount" :advance_payment_amount="advance_payment_amount" :wallet_amount = "wallet_amount" v-if="isChildComponentVisible" />
            </div>
          </form>
        </div>


        <div class="modal fade show couponmodal" id="taxModal" v-if="is_tax == 1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: block;" >
          <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">{{$t('messages.applied_taxes')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"  @click="closeModal()"></button>
              </div>
              <div class="modal-body">
                  <div class="d-flex justify-content-between" v-for="tax in taxes" :key="tax">
                    <p>{{ tax.title }}</p>
                    <p v-if="tax.type == 'percent'">{{ formatCurrencyVue(tax.value*subtotal/100) }}</p>
                    <p v-else>{{ formatCurrencyVue(tax.value) }}</p>
                  </div>
              </div>
            </div>
          </div>  
        </div>
    
    </div>
</template> 

<script setup>
import { ref, defineProps,computed,onMounted} from 'vue';
import Payment from '../components/Payment.vue';
import FlatPickr from 'vue-flatpickr-component'
import { useField, useForm } from 'vee-validate'
import 'flatpickr/dist/flatpickr.css';
import * as yup from 'yup';
import { STORE_BOOKING_API} from '../data/api'; 
import couponcard from '../components/CouponCard.vue';
import { confirmcancleSwal} from '../data/utilities'; 
import Swal from 'sweetalert2-neutral';
import { Calendar, DatePicker } from 'v-calendar';
import 'v-calendar/style.css';
import moment from 'moment'
const baseUrl = document.querySelector('meta[name="baseUrl"]').getAttribute('content');
const props = defineProps(['service','coupons','taxes','user_id','availableserviceslot','serviceaddon','googlemapkey','wallet_amount']);
console.log(props.service.service_id);
const googlemapkey = props.googlemapkey;
const maxDate = computed(() => {
    return props.service.end_at ? new Date(props.service.end_at) : null;
});

// Flatpickr configuration
const config = {
    enableTime: true, // if you want to allow time selection
    dateFormat: 'Y-m-d H:i', // your preferred date format
    minDate: 'today', // optional, to disable past dates
    maxDate: maxDate.value, // this limits the selection to service.end_at
};
// const config = ref({
//       enableTime: true,
//       dateFormat: 'Y-m-d H:i',
//       static: true,
//       minDate: 'today',
// });

const todos = ref([
  {
    highlight: true,  
  },
]);

const is_tax = ref(0)
const openTaxModel = () =>{
  is_tax.value = 1;
}
const closeModal = () =>{
  is_tax.value = 0;
}


onMounted(() => {

    defaultData()
    handleDateSelect(new Date())

    if(props.serviceaddon){
      calculateAddonAmount()
    }
})
const padZero = (num) => num.toString().padStart(2, '0');

const formatDate = (dateString) => {
  const datefrm = window.dateformate || 'Y-m-d';
  const date = new Date(dateString);

  const year = date.getFullYear();
  const month = padZero(date.getMonth() + 1);
  const day = padZero(date.getDate());

  const ordinalSuffix = (day) => ['th', 'st', 'nd', 'rd'][(day % 10 > 3 || [11, 12, 13].includes(day)) ? 0 : day % 10];

  const formatMap = {
    'Y-m-d': `${year}-${month}-${day}`,
    'm-d-Y': `${month}-${day}-${year}`,
    'd-m-Y': `${day}-${month}-${year}`,
    'd/m/Y': `${day}/${month}/${year}`,
    'm/d/Y': `${month}/${day}/${year}`,
    'Y/m/d': `${year}/${month}/${day}`,
    'Y.m.d': `${year}.${month}.${day}`,
    'd.m.Y': `${day}.${month}.${year}`,
    'm.d.Y': `${month}.${day}.${year}`,
    'jS M Y': `${date.getDate()}${ordinalSuffix(date.getDate())} ${date.toLocaleString('default', { month: 'short' })} ${year}`,
    'M jS Y': `${date.toLocaleString('default', { month: 'short' })} ${date.getDate()}${ordinalSuffix(date.getDate())} ${year}`,
    'D, M d, Y': `${date.toLocaleString('default', { weekday: 'short' })}, ${date.toLocaleString('default', { month: 'short' })} ${day}, ${year}`,
    'D, d M, Y': `${date.toLocaleString('default', { weekday: 'short' })}, ${day} ${date.toLocaleString('default', { month: 'short' })}, ${year}`,
    'D, M jS Y': `${date.toLocaleString('default', { weekday: 'short' })}, ${date.toLocaleString('default', { month: 'short' })} ${date.getDate()}${ordinalSuffix(date.getDate())} ${year}`,
    'D, jS M Y': `${date.toLocaleString('default', { weekday: 'short' })}, ${date.getDate()}${ordinalSuffix(date.getDate())} ${date.toLocaleString('default', { month: 'short' })} ${year}`,
    'F j, Y': `${date.toLocaleString('default', { month: 'long' })} ${date.getDate()}, ${year}`,
    'd F, Y': `${date.getDate()} ${date.toLocaleString('default', { month: 'long' })}, ${year}`,
    'jS F, Y': `${date.getDate()}${ordinalSuffix(date.getDate())} ${date.toLocaleString('default', { month: 'long' })}, ${year}`,
    'l jS F Y': `${date.toLocaleString('default', { weekday: 'long' })} ${date.getDate()}${ordinalSuffix(date.getDate())} ${date.toLocaleString('default', { month: 'long' })} ${year}`,
    'l, F j, Y': `${date.toLocaleString('default', { weekday: 'long' })}, ${date.toLocaleString('default', { month: 'long' })} ${date.getDate()}, ${year}`
  };

  return formatMap[datefrm] || `${year}-${month}-${day}`;
};
const formattedDuration = (value) => {
  if (!value) return '';

  const durationParts = value.split(':');
  const hours = parseInt(durationParts[0]);
  const minutes = parseInt(durationParts[1]);

  // Hide if duration is exactly 0 hrs and 0 min
  if (hours === 0 && minutes === 0) return '';

  if (hours > 0) {
    return `(${hours} hrs${minutes > 0 ? ' ' + minutes + ' min' : ''})`;
  } else {
    return `(${minutes} min)`;
  }
};


const DateFormate = ref(new Date());

const config1 = ref({
      enableTime: false,
      dateFormat: 'Y-m-d',
      static: true
});

const dayName=ref(null)

const dateObject=ref(null)

const handleDateSelect=(value)=>{

    if(value==null){

        todos.value.highlight=true;
    }else{

      dateObject.value = new Date(value);
      const daysOfWeek = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"]
      dayName.value = daysOfWeek[dateObject.value.getDay()]

    }

}



const isChildComponentVisible = ref(false);
const currentComponent = ref(null);

const showChildComponent = () => {
  currentComponent.value = Payment;
  isChildComponentVisible.value = true;
};

const quantity=ref(1)
const OpenCouponCard=ref(0)
const SeletedCouponId=ref(0)
const IsLoading=ref(0)
const selectedCoupon = ref([]);
const bookingId=ref(null)

const OpenCouponCardMethod = () => {
  OpenCouponCard.value = 1
};

const RemoveCoupon=()=>{

    SeletedCouponId.value=0
    selectedCoupon.value=null
}

const addonAmount = ref(0);

const calculateAddonAmount = () => {
  addonAmount.value = props.serviceaddon.reduce((total, addon) => total + addon.price, 0);
};

const removeAddons = (index) => {
  Swal.fire({
    title: 'Do you want to remove this Add-on Service?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes'
  }).then((result) => {
    if (result.isConfirmed) {
      const removedService = props.serviceaddon.splice(index, 1)[0];
      addonAmount.value -= removedService.price;
    }
  });
};


const increment = () => {  
    quantity.value = quantity.value+1
};

const decrement = () => {
    if(quantity.value !=1){
        quantity.value = quantity.value-1
      }
};

const subtotal = computed(() => {

 if(coupondiscount.value >0){
    if(props.serviceaddon){
      return (props.service.price*quantity.value)+addonAmount.value - discount.value - coupondiscount.value
    }else{
      return (props.service.price*quantity.value) - discount.value - coupondiscount.value
    }
   
 }else{

    if(props.serviceaddon){
      return (props.service.price*quantity.value)+addonAmount.value - discount.value
    }else{
      return (props.service.price*quantity.value) - discount.value
    }

  }
  
});

const taxAmount = computed(() => {
  let totalTaxAmount = 0

  if(props.taxes != null && subtotal.value > 0){

    for(const tax of props.taxes) {

        if (tax.type === 'percent') {
        
           totalTaxAmount += (( subtotal.value) *tax.value) / 100;
        
        } else {
          totalTaxAmount += tax.value;
        }

      }
        return totalTaxAmount;
     
    }

});

const discount = computed(() => {

 if(props.service.discount !='' && props.service.discount >0){
         
     return  props.service.price*quantity.value*props.service.discount/100
 }

 return 0

});


const coupondiscount = computed(() => {

 if(selectedCoupon.value !=null){
    if(selectedCoupon.value.discount_type=='fixed'){   
        return  selectedCoupon.value.discount
    }else{

        return  props.service.price*quantity.value*selectedCoupon.value.discount/100
    }
  }
 
  return 0
    
 });

 const  totalAmount = computed(() => {

  return taxAmount.value+ subtotal.value;
   
});

const advance_payment_amount = computed(() => {

  const rawValue = totalAmount.value * props.service.advance_payment_amount / 100;
  const roundedValue = Number(rawValue).toFixed(2);
  return parseFloat(roundedValue); 
 
});




const formatCurrencyVue = (value) => {

  if(window.currencyFormat !== undefined) {
    return window.currencyFormat(value)
  }
  return value
}


const handleCouponResponse = (couponId) => {
  if (couponId != null) {
    const foundCoupon = props.coupons.find(coupon => coupon.id == couponId);

    if (foundCoupon) {
      let discountAmount = foundCoupon.discount;

      if (foundCoupon.discount_type === 'percent') {
        discountAmount = (foundCoupon.discount / 100) * props.service.price;
      }

      // Reject coupon if discount exceeds price
      if (discountAmount > props.service.price) {
        alert("This coupon cannot be applied because the discount exceeds the service price.");
        return;
      }

      selectedCoupon.value = foundCoupon;
      SeletedCouponId.value = couponId;
    } else {
      selectedCoupon.value = null;
      SeletedCouponId.value = 0;
    }

    OpenCouponCard.value = 0;
  } else {
    SeletedCouponId.value = 0;
    selectedCoupon.value = null;
  }
};


const defaultData = () => {
  errorMessages.value = {}
  return {
    address: '',
    date: new Date(),
    start_time:''
  }
}


const validationSchema = yup.object({
    address: yup.string().required('Address is Required'),

    date: yup.string().test('date', "Date and Time is Required", function(value) {
        const { service } = props;

        // If service is not using slots and the date is empty, throw validation error
        if (service.is_slot != 1 && !value) {
            return false;
        }

        // If end_at exists, ensure the selected date is before or equal to end_at
        if (service.end_at) {
            const selectedDate = new Date(value); // Parse the value into a JavaScript Date object
            const endDate = new Date(service.end_at);

            if (selectedDate > endDate) {
                return this.createError({ message: 'Selected date exceeds the allowed package end date' });
            }
        }

        return true;
    }),

   start_time: yup.string().test('start_time', "Please Select Time Slot", function(value) {
       
       if(props.service.is_slot == 1 && !value  ) {
           
          return false ;
         }
         return true;
      }),
   
})

const { handleSubmit, errors, resetForm,setValues } = useForm({
  validationSchema,
})
const { value: address } = useField('address')
const { value: date } = useField('date')
const { value: start_time } = useField('start_time')
const isLoading = ref(false); 
const getCurrentLocation = async () => {
  isLoading.value = true;
  navigator.geolocation.getCurrentPosition(async (position) => {
    try {
       
        const currentLatitude = position.coords.latitude;
        const currentLongitude = position.coords.longitude;

        const response = await fetch(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${currentLatitude},${currentLongitude}&key=${googlemapkey}`);
        const data = await response.json();

        const formattedAddress = data.results[0]?.formatted_address;

        setValues({ address: formattedAddress });

        // localStorage.setItem('location_current_lat', currentLatitude);
        // localStorage.setItem('location_current_long', currentLongitude);
    } catch (error) {
        console.error('Error fetching current location:', error);
    }finally {
            isLoading.value = false; // Set isLoading to false after location is fetched
          }
  }, (error) => {
    console.error('Error getting current position:', error);
    isLoading.value = false; 
  });
};
const cancellation = window.cancellationCharge;
let cancellationCharge = 0;

// Calculate cancellation charge if applicable
if (cancellation['cancellation_charge'] == 1) {
    cancellationCharge = (props.service.price * cancellation['cancellation_charge_amount']) / 100;
}

const errorMessages = ref({})
const formSubmit = handleSubmit(async(values) => {

    IsLoading.value=1
 
    const title='Confirm Booking '

    const subtitle='Do you want to Confirm this booking ?'

    let note = '';

    // Add note about cancellation charge if applicable
    if (cancellationCharge > 0) {
        note = `A ${formatCurrencyVue(cancellationCharge)} fee applies for cancellation within ${cancellation['cancellation_charge_hours']} hours of the scheduled service.`;
    }
    

    confirmcancleSwal({ title: title, subtitle:subtitle, text: note }).then(async(result) => {
      IsLoading.value=0
    if (!result.isConfirmed) return
    IsLoading.value=1
    const csrfToken = document.head.querySelector("[name~=csrf-token][content]").content;

    if(props.service.is_slot==1){

        values.date = moment(dateObject.value).format('YYYY-MM-DD')+ ' ' + values.start_time;
        values.booking_slot = values.start_time;
     }

    values.id = '';
    values.customer_id = props.user_id;
    values.service_id = props.service.package_type ? props.service.service_id : props.service.id;
    values.provider_id = props.service.provider_id;
    values.amount = props.service.price;
    values.quantity = quantity.value;
    values.total_amount = totalAmount.value;
    values.discount = props.service.discount;
    values.type = props.service.service_type;
    values.final_total_service_price = props.service.price * quantity.value;
    values.final_total_tax = taxAmount.value;
    values.final_sub_total = subtotal.value;
    values.final_discount_amount = discount.value;
    values.tax =props.taxes;
    values.status = 'pending';

    if (props.service.package_type) {
    const uniqueServiceIds = [
      ...new Set(props.service.package_services.map((service) => service.service_id))
    ];

    values.booking_package = {
      id: props.service.id,
      name: props.service.name,
      is_featured: props.service.is_featured,
      package_type: props.service.package_type,
      price: props.service.price,
      start_at: props.service.start_at,
      end_at: props.service.end_at,
      subcategory_id: props.service.subcategory_id,
      category_id: props.service.category_id,
      service_id: uniqueServiceIds.join(','),
    };
  }
    if(props.serviceaddon){
      values.service_addon_id = props.serviceaddon.map(addon => addon.id);
    }

    if(SeletedCouponId.value>0){
      //values.coupon_id=SeletedCouponId.value
      values.coupon_id=selectedCoupon.value.code
      values.final_coupon_discount_amount=coupondiscount.value
    
    }else{
        values.coupon_id = ''
     }

     const response = await fetch(STORE_BOOKING_API, {
           method: 'POST',
           headers: {
          
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
           },
           body:JSON.stringify(values),
        });
        if(response.ok){

          IsLoading.value=0

         const responseData = await response.json();

        if(props.service.is_enable_advance_payment==1){

          bookingId.value=responseData.booking_id

          showChildComponent()

         }else{

          IsLoading.value=0
          Swal.fire({
          title: 'Done',
          text: responseData.message,
          icon: 'success',
          iconColor: '#5F60B9'
        }).then((result) => {

            if (result.isConfirmed) {
               const baseUrl = document.querySelector('meta[name="baseUrl"]').getAttribute('content');
               window.location.href = baseUrl + '/booking-list';
             }

          })

         }
        
        } else {

          IsLoading.value=0

            Swal.fire({
              title: 'Error',
              text: 'Something Went Wrong!',
              icon: 'error',
              iconColor: '#5F60B9'
            }).then((result) => {
    
            })
        }

     })

})    

const validCoupons = computed(() => {
  return props.coupons.filter(coupon => {
    let discountAmount = coupon.discount;

    if (coupon.discount_type === 'percent') {
      discountAmount = (coupon.discount / 100) * props.service.price;
    }

    return discountAmount <= props.service.price;
  });
});

</script>

<header>
      @php
         $headerSection = App\Models\FrontendSetting::where('key', 'heder-menu-setting')->first();
         $sectionData = $headerSection ? json_decode($headerSection->value, true) : null;
      @endphp
    <div class="top-header bg-primary">
       <div class="container-fluid">
          <div class="row align-items-center">
             <div class="col-6">
                <ul class="top-header-left list-inline d-flex align-items-center gap-3 m-0">
                   <li>
                        @php
                           $appsettings = App\Models\AppSetting::first();
                           $generalsetting = App\Models\Setting::where('type','general-setting')->where('key', 'general-setting')->first();
                           $appsetting = $generalsetting ? json_decode($generalsetting->value) : null;

                        @endphp
                      <a class="text-white d-flex align-items-center" href="tel:{{ optional($appsetting)->helpline_number }}">
                         <svg class="me-2" height="16" width="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                               d="M11.5317 12.4724C15.5208 16.4604 16.4258 11.8467 18.9656 14.3848C21.4143 16.8328 22.8216 17.3232 19.7192 20.4247C19.3306 20.737 16.8616 24.4943 8.1846 15.8197C-0.493478 7.144 3.26158 4.67244 3.57397 4.28395C6.68387 1.17385 7.16586 2.58938 9.61449 5.03733C12.1544 7.5765 7.54266 8.48441 11.5317 12.4724Z"
                               stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            </path>
                         </svg>
                         <span>{{ optional($appsetting)->helpline_number }}</span>
                      </a>
                   </li>
                </ul>
             </div>
             <div class="col-6 text-end">
                <div class="d-inline-block position-relative dropdown">
                  @if ($sectionData && isset($sectionData['header_setting']) && $sectionData['header_setting'] == 1)
                  @if($sectionData['enable_language'] == 1)
                     <a class="text-white d-flex align-items-center" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="true" aria-expanded="true">
                        {{strtoupper(app()->getLocale())}}
                        <svg width="8" class="ms-1 transform-up" viewBox="0 0 12 8" fill="none"
                           xmlns="http://www.w3.org/2000/svg">
                           <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M6 5.08579L10.2929 0.792893C10.6834 0.402369 11.3166 0.402369 11.7071 0.792893C12.0976 1.18342 12.0976 1.81658 11.7071 2.20711L6.70711 7.20711C6.31658 7.59763 5.68342 7.59763 5.29289 7.20711L0.292893 2.20711C-0.0976311 1.81658 -0.0976311 1.18342 0.292893 0.792893C0.683418 0.402369 1.31658 0.402369 1.70711 0.792893L6 5.08579Z"
                              fill="currentColor"></path>
                        </svg>
                     </a>
                     <div class="dropdown-menu dropdown-menu-end">
                        <?php
                              $language_option = sitesetupSession('get')->language_option ?? ["ar","nl","en","fr","de","hi","it"];
                              if (!empty($language_option)) {
                                 $language_array = languagesArray($language_option);
                              }
                        ?>
                        @if(count($language_array) > 0 )
                        @foreach( $language_array as $lang )
                           <a class="dropdown-item d-block" href="{{ route('switch-language',['locale'=> $lang['id'] ]) }}">
                              {{ $lang['title'] }}
                           </a>
                        @endforeach
                        @endif
                     </div>
                  @endif
                  @endif
                </div>

             </div>
          </div>
       </div>
    </div>
    <nav class="nav navbar navbar-expand-xl navbar-light iq-navbar header-hover-menu py-xl-0 ">
       <div class="container-fluid navbar-inner">
          <div class="d-flex align-items-center justify-content-between w-100 landing-header">
             <div class="d-flex gap-3 gap-xl-0 align-items-center">
                <div>
                   <button data-trigger="navbar_main" id="res_sidebar"
                      class="d-xl-none btn btn-primary rounded-pill p-1 pt-0 toggle-rounded-btn lh-base" type="button">
                      <svg width="20px" class="icon-20" viewBox="0 0 24 24">
                         <path fill="currentColor"
                            d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z"></path>
                      </svg>
                   </button>
                </div>
                <!--Logo -->
                @include('landing-page.components.widgets.header_logo')
             </div>
             <!-- navigation -->
             @include('landing-page.partials._horizontal-nav')

             <div class="right-panel">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                   data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                   aria-label="Toggle navigation">
                   <span class="navbar-toggler-btn">
                      <span class="navbar-toggler-icon"></span>
                   </span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                   <ul class="navbar-nav align-items-center ms-auto mb-2 mb-xl-0 p-0">
                      <!-- Dropdown Notificaton -->
                     @if ($sectionData && isset($sectionData['header_setting']) && $sectionData['header_setting'] == 1)
                     @if($sectionData['enable_darknight_mode'] == 1)
                      <li class="nav-item theme-scheme-dropdown dropdown iq-dropdown">
                         <a href="javascript:void(0)" class="nav-link d-flex align-items-center change-mode">
                            <svg class="mode-icons light-mode" width="18" height="18" fill="currentColor"
                               xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024">
                               <path
                                  d="M512 211c-167.8 0-304.4 136.6-304.4 304.4 0 167.8 136.6 304.4 304.4 304.4 167.8 0 304.4-136.6 304.4-304.4 0-168-136.6-304.4-304.4-304.4zm0 527c-122.8 0-222.8-100-222.8-222.8 0-122.8 100-222.8 222.8-222.8 122.8 0 222.8 100 222.8 222.8C734.8 638 634.8 738 512 738zm0-588.4c22.6 0 40.8-18.2 40.8-40.8v-46c0-22.6-18.2-40.8-40.8-40.8-22.6 0-40.8 18.2-40.8 40.8v46c0 22.6 18.2 40.8 40.8 40.8zm0 724.8c-22.6 0-40.8 18.2-40.8 40.8V961c0 22.6 18.2 40.8 40.8 40.8 22.6 0 40.8-18.2 40.8-40.8v-45.8c0-22.4-18.2-40.8-40.8-40.8zm449.2-403.2h-46c-22.6 0-40.8 18.2-40.8 40.8 0 22.6 18.2 40.8 40.8 40.8h46c22.6 0 40.8-18.2 40.8-40.8 0-22.6-18.2-40.8-40.8-40.8zm-852.4 0h-46C40.2 471.2 22 489.4 22 512c0 22.6 18.2 40.8 40.8 40.8h45.8c22.6 0 40.8-18.2 40.8-40.8.2-22.6-18.2-40.8-40.6-40.8zm692-305.6L768.2 198c-16 16-16 41.8 0 57.8s41.8 16 57.8 0l32.4-32.4c16-16 16-41.8 0-57.8s-41.8-16-57.6 0zM198 768.2l-32.4 32.4c-16 16-16 41.8 0 57.8s41.8 16 57.8 0l32.4-32.4c16-16 16-41.8 0-57.8s-41.8-15.8-57.8 0zm628 0c-16-16-41.8-16-57.8 0s-16 41.8 0 57.8l32.4 32.4c16 16 41.8 16 57.8 0s16-41.8 0-57.8L826 768.2zM198 255.8c16 16 41.8 16 57.8 0s16-41.8 0-57.8l-32.4-32.4c-16-16-41.8-16-57.8 0s-16 41.8 0 57.8l32.4 32.4z" />
                            </svg>
                            <svg class="mode-icons dark-mode" width="18" height="18" fill="currentColor"
                               xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024.0412 1024">
                               <path
                                  d="M516.087 1006.5c-188.85 0-370.925-104.172-461.077-284.19C-69.442 473.677 23.964 174.315 267.7 40.744L310.13 17.5 297.468 64.19c-30.45 112.387-18.508 231.61 33.62 335.7 56.615 113.086 153.91 197.37 273.916 237.29 120.045 39.92 248.388 30.717 361.495-25.9 5.146-2.572 10.128-5.31 15.11-8.05l42.43-23.262-12.043 46.362C973.643 767.83 876.594 886.54 745.74 952.067 671.954 989 593.434 1006.48 516.087 1006.5zM247.09 101.62C53.3 233.956-15.522 489.426 91.86 703.865c116.897 233.462 401.91 328.267 635.413 211.392C833.38 862.1 915.36 770.897 957.46 660.857c-115.947 49.966-244.62 55.628-365.467 15.4-130.463-43.398-236.2-134.992-297.757-257.92-49.266-98.387-65.51-209.168-47.145-316.717z" />
                            </svg>
                         </a>
                      </li>
                     @endif
                     @endif
                      <!-- Dropdown Notificaton -->
                      <!-- Wishlist -->
                      @if(auth()->check() && auth()->user()->user_type == 'user')
                        <li class="nav-item">
                            <a href="{{route('favourite.service')}}" class="nav-link" id="wishlist">
                              <div class="nav-list-icon">
                                 <div class="btn-inner">
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                       xmlns="http://www.w3.org/2000/svg">
                                       <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M1.39326 8.66527C0.499095 5.8736 1.5441 2.68277 4.47493 1.7386C6.0166 1.2411 7.71826 1.53443 8.99993 2.4986C10.2124 1.5611 11.9766 1.24443 13.5166 1.7386C16.4474 2.68277 17.4991 5.8736 16.6058 8.66527C15.2141 13.0903 8.99993 16.4986 8.99993 16.4986C8.99993 16.4986 2.8316 13.1419 1.39326 8.66527Z"
                                          stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                          stroke-linejoin="round" />
                                       <path d="M12.3334 4.58325C13.225 4.87159 13.855 5.66742 13.9309 6.60158"
                                          stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                          stroke-linejoin="round" />
                                    </svg>
                                 </div>
                              </div>
                           </a>
                        </li>
                      @endif
                      <!-- Wishlist -->
                      @if(empty(auth()->user()) || auth()->user()->user_type !== 'user')
                        <li class="ms-sm-3 ms-2">
                           <a href="{{route('user.login')}}" class="btn btn btn-outline-primary" role="button">
                              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="16" viewBox="0 0 14 16"
                                 fill="none">
                                 <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M6.98857 10.5095C4.08786 10.5095 1.61072 10.9481 1.61072 12.7045C1.61072 14.461 4.07215 14.9152 6.98857 14.9152C9.88929 14.9152 12.3657 14.476 12.3657 12.7202C12.3657 10.9645 9.905 10.5095 6.98857 10.5095Z"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                 <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M6.98854 8.00441C8.89212 8.00441 10.435 6.46084 10.435 4.55727C10.435 2.6537 8.89212 1.11084 6.98854 1.11084C5.08497 1.11084 3.5414 2.6537 3.5414 4.55727C3.53497 6.45441 5.06783 7.99798 6.96426 8.00441H6.98854Z"
                                    stroke="currentColor" stroke-width="1.42857" stroke-linecap="round"
                                    stroke-linejoin="round" />
                              </svg>
                              {{__('messages.login')}}
                           </a>
                        </li>
                      @else

                      @if(auth()->check() && auth()->user()->user_type == 'user')
                           <li class="nav-item dropdown user-dropdown" id="itemdropdown1">
                              <a class="nav-link d-flex align-items-center" href="#" id="navbarDropdown" role="button"
                                 data-bs-toggle="dropdown" aria-expanded="false">
                                 <div class="icon-50">
                                    <span class="btn-inner d-inline-block position-relative">
                                       <img src="{{ getSingleMedia(auth()->user(),'profile_image') }}"
                                          class="img-fluid avatar-50 rounded-circle object-cover" alt="icon">
                                    </span>
                                 </div>
                              </a>
                              <ul class="dropdown-menu dropdown-menu-end user-dropdown-menu p-0"
                                 aria-labelledby="navbarDropdown">
                                 <li class="d-flex align-items-center justify-content-start gap-3 p-3">
                                    <img src="{{ getSingleMedia(auth()->user(),'profile_image') }}"
                                       class="img-fluid avatar-30 rounded object-cover" alt="icon">
                                    <h6>{{ auth()->user()->first_name." ".auth()->user()->last_name }}</h6>
                                 </li>
                                 <li>
                                    <hr class="dropdown-divider mt-0 mb-2">
                                 </li>
                                 <li>
                                    <a class="dropdown-item" href="{{ route('home') }}">
                                       <svg xmlns="http://www.w3.org/2000/svg" width="12" height="15" viewBox="0 0 12 15"
                                          fill="none">
                                          <g id="Profile">
                                             <g id="Group 6">
                                                <g id="Union">
                                                   <path fill-rule="evenodd" clip-rule="evenodd"
                                                      d="M5.92585 7.91259H5.94718C7.89918 7.91259 9.48651 6.32525 9.48651 4.37325C9.48651 2.42125 7.89918 0.833252 5.94718 0.833252C3.99451 0.833252 2.40652 2.42125 2.40652 4.37125C2.39985 6.31792 3.97785 7.90659 5.92585 7.91259ZM3.35852 4.37325C3.35852 2.94592 4.51985 1.78525 5.94718 1.78525C7.37385 1.78525 8.53451 2.94592 8.53451 4.37325C8.53451 5.79992 7.37385 6.96125 5.94718 6.96125H5.92785C4.50652 6.95592 3.35385 5.79592 3.35852 4.37325Z"
                                                      fill="currentColor" />
                                                   <path fill-rule="evenodd" clip-rule="evenodd"
                                                      d="M0.666504 11.6155C0.666504 14.0801 4.64117 14.0801 5.94717 14.0801C8.21317 14.0801 11.2265 13.8261 11.2265 11.6288C11.2265 9.16414 7.25317 9.16414 5.94717 9.16414C3.6805 9.16414 0.666504 9.41814 0.666504 11.6155ZM1.6665 11.6155C1.6665 10.6521 3.1065 10.1641 5.94717 10.1641C8.78717 10.1641 10.2265 10.6568 10.2265 11.6288C10.2265 12.5921 8.78717 13.0801 5.94717 13.0801C3.1065 13.0801 1.6665 12.5875 1.6665 11.6155Z"
                                                      fill="currentColor" />
                                                </g>
                                             </g>
                                          </g>
                                       </svg>
                                       <span class="ms-2"> {{__('messages.dashboard')}}</span>
                                    </a>
                                 </li>

                                 {{-- <li>
                                    <span class="dropdown-item"> --}}
                                       @php
                                       $user = auth()->user();
                                       $wallet = $user->wallet ?? null;
                                       $wallet_amount = 0;
                                       $othersetting = \App\Models\Setting::where('type', 'OTHER_SETTING')->where('key', 'OTHER_SETTING')->first();
                                       $wallet_enabled = $othersetting && !empty(json_decode($othersetting->value)->wallet);

                                       if ($wallet_enabled && $wallet) {
                                           $wallet_amount = $wallet->amount ?? 0;
                                       }
                                       @endphp
                                       @if($wallet_enabled)
                                       <li>
                                          <span class="dropdown-item">
                                          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <path d="M2 12C2 8.22876 2 6.34315 3.17157 5.17157C4.34315 4 6.22876 4 10 4H14C17.7712 4 19.6569 4 20.8284 5.17157C22 6.34315 22 8.22876 22 12V14C22 17.7712 22 19.6569 20.8284 20.8284C19.6569 22 17.7712 22 14 22H10C6.22876 22 4.34315 22 3.17157 20.8284C2 19.6569 2 17.7712 2 14V12Z" stroke="currentColor" stroke-width="1.5"></path>
                                          <path d="M18 16L16 16M16 16L14 16M16 16L16 14M16 16L16 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                                          <path d="M7 4V2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                                          <path d="M17 4V2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                                          <path d="M2.5 9H21.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                                       </svg>
                                       <span class="ms-2">{{__('messages.wallet_balance')}}: <span class="text-primary">{{getPriceFormat($wallet_amount)}}</span></span>
                                       @endif
                                    </span>
                                 </li>
                                 @if(auth()->user()->can('helpdesk list'))
                                 <li>
                                    <a class="dropdown-item" href="{{ route('helpdesk.list') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 120 121" fill="none">
                            <path d="M119.263 81.796C119.263 77.4137 117.585 73.2739 114.601 70.0851V56.3228C114.601 41.7587 108.932 28.0524 98.6199 17.7214C88.2889 7.39041 74.5826 1.72142 59.9812 1.72142C29.8833 1.68412 5.37978 26.1876 5.37978 56.3042V70.0664C2.41474 73.2366 0.717773 77.3951 0.717773 81.796C0.717773 90.7844 7.63619 98.169 16.4194 98.8962C17.3145 102.085 20.205 104.435 23.6735 104.435C27.8506 104.435 31.2446 101.041 31.2446 96.8636V66.7284C31.2446 62.5513 27.8506 59.1573 23.6735 59.1573C20.7271 59.1573 18.2096 60.8543 16.9415 63.3158V56.2855C16.9415 32.5652 36.2422 13.2459 59.9625 13.2459C71.4311 13.2459 82.2469 17.7214 90.3961 25.852C98.5266 34.0198 103.002 44.817 103.002 56.2855V63.2972C101.753 60.8543 99.2166 59.1573 96.2889 59.1573C92.1117 59.1573 88.7178 62.5513 88.7178 66.7284V96.8636C88.7178 101.041 92.1117 104.435 96.2889 104.435C99.7574 104.435 102.648 102.085 103.543 98.8962C103.767 98.8776 103.99 98.8589 104.214 98.8216V100.929C104.214 106.337 99.8133 110.719 94.4241 110.719H72.5313V110.216C72.5313 107.735 70.5173 105.74 68.0371 105.74H54.7597C52.2795 105.74 50.2842 107.754 50.2842 110.216V114.803C50.2842 117.283 52.2982 119.297 54.7597 119.297H68.0371C70.5173 119.297 72.5313 117.283 72.5313 114.803V114.318H94.4241C101.809 114.318 107.813 108.313 107.813 100.929V97.9638C114.471 95.6142 119.263 89.2552 119.263 81.796ZM68.0371 115.717H54.7597C54.2749 115.717 53.8646 115.307 53.8646 114.822V110.234C53.8646 109.749 54.2562 109.339 54.7597 109.339H68.0371C68.5406 109.339 68.9322 109.731 68.9322 110.234V112.509V112.528V112.547V114.822C68.9322 115.307 68.5406 115.717 68.0371 115.717ZM4.31684 81.796C4.31684 77.6562 6.18164 73.7774 9.4264 71.1666C10.0791 70.6631 10.6572 70.2715 11.2912 69.9359C11.3471 69.9172 11.3844 69.8799 11.4404 69.8613C11.9812 69.5629 12.5406 69.3205 13.1187 69.0967C13.2306 69.0594 13.3425 69.0035 13.4544 68.9662C14.107 68.7424 14.7784 68.5559 15.4684 68.444H15.487C15.6921 68.4067 15.8973 68.3694 16.1024 68.3321H16.1397V95.2599C9.48234 94.3834 4.31684 88.6771 4.31684 81.796ZM23.6921 62.7564C25.8926 62.7564 27.6642 64.5466 27.6642 66.7284V96.8636C27.6642 99.0641 25.8739 100.836 23.6921 100.836C21.6036 100.836 19.9252 99.2319 19.7574 97.1806V97.162L19.7201 66.5792C19.8133 64.4534 21.5476 62.7564 23.6921 62.7564ZM92.9509 23.3345C84.1117 14.5326 72.4008 9.68412 59.9812 9.68412C34.2842 9.68412 13.3798 30.6072 13.3798 56.3042V65.2179C13.2865 65.2366 13.2119 65.2739 13.1187 65.3112C12.6712 65.4417 12.2422 65.5909 11.8133 65.7401C11.6082 65.8147 11.4031 65.8893 11.198 65.9825C10.7318 66.1876 10.2842 66.4114 9.83665 66.6538C9.74341 66.7098 9.63153 66.7471 9.53829 66.803C9.3518 66.9149 9.14668 67.0268 8.97884 67.12V56.3228C8.97884 28.183 31.86 5.30184 59.9812 5.30184C73.6129 5.30184 86.4427 10.6165 96.0837 20.2575C105.725 29.9172 111.021 42.7284 111.021 56.3228V67.12C110.834 67.0081 110.648 66.8962 110.461 66.803C110.331 66.7284 110.2 66.6725 110.051 66.6165C109.622 66.3927 109.193 66.1876 108.764 66.0011C108.578 65.9266 108.373 65.852 108.186 65.7774C107.776 65.6095 107.347 65.479 106.918 65.3485C106.806 65.3112 106.713 65.2739 106.601 65.2366V56.3228C106.601 43.8846 101.753 32.1736 92.9509 23.3345ZM96.3075 100.836C94.107 100.836 92.3355 99.0454 92.3355 96.8636V66.7284C92.3355 64.5279 94.1257 62.7564 96.3075 62.7564C98.4707 62.7564 100.242 64.5093 100.28 66.6538L100.261 97.1247C100.112 99.1946 98.3961 100.836 96.3075 100.836ZM103.879 95.2599V68.3321H103.916C104.177 68.3694 104.419 68.4254 104.662 68.4627C105.296 68.5746 105.93 68.7424 106.545 68.9662C106.639 69.0035 106.732 69.0408 106.844 69.0781C107.422 69.3018 108 69.5443 108.541 69.8613C108.597 69.8986 108.652 69.9172 108.727 69.9545C109.324 70.2715 109.902 70.6631 110.536 71.148C113.799 73.7587 115.683 77.6375 115.683 81.796C115.683 88.6585 110.555 94.3648 103.879 95.2599Z" fill="#6C757D"/>
                            <path d="M57.2399 81.8333C57.9299 82.6538 58.9368 83.12 59.9998 83.12C61.0627 83.12 62.0697 82.6538 62.7597 81.8333L68.1676 75.3997H79.5243C82.5079 75.3997 84.9322 72.9755 84.9322 69.9918V44.2948C84.9322 41.3111 82.5079 38.8869 79.5243 38.8869H40.4753C37.4916 38.8869 35.0674 41.3111 35.0674 44.2948V70.0104C35.0674 72.9941 37.4916 75.4184 40.4753 75.4184H51.832L57.2399 81.8333ZM38.6665 70.0104V44.2948C38.6665 43.2878 39.487 42.486 40.4753 42.486H79.5243C80.5313 42.486 81.3331 43.3065 81.3331 44.2948V70.0104C81.3331 71.0174 80.5126 71.8193 79.5243 71.8193H67.3285C66.8063 71.8193 66.3028 72.0617 65.9485 72.4533L59.9998 79.5209L54.0511 72.4533C53.7154 72.0431 53.2119 71.8193 52.6711 71.8193H40.4753C39.4683 71.8193 38.6665 70.9988 38.6665 70.0104Z" fill="#6C757D"/>
                            <path d="M45.6037 51.4743H74.3962C75.3846 51.4743 76.1864 50.6725 76.1864 49.6841C76.1864 48.6958 75.3846 47.8939 74.3962 47.8939H45.6037C44.6153 47.8939 43.8135 48.6958 43.8135 49.6841C43.8135 50.6725 44.6153 51.4743 45.6037 51.4743Z" fill="currentColor"/>
                            <path d="M45.6037 59.2132H74.3962C75.3846 59.2132 76.1864 58.4114 76.1864 57.423C76.1864 56.4347 75.3846 55.6328 74.3962 55.6328H45.6037C44.6153 55.6328 43.8135 56.4347 43.8135 57.423C43.8135 58.4114 44.6153 59.2132 45.6037 59.2132Z" fill="currentColor"/>
                            <path d="M60.7272 65.1807C60.7272 64.1923 59.9254 63.3904 58.937 63.3904H45.6037C44.6153 63.3904 43.8135 64.1923 43.8135 65.1807C43.8135 66.169 44.6153 66.9709 45.6037 66.9709H58.937C59.9254 66.9709 60.7272 66.169 60.7272 65.1807Z" fill="currentColor"/>
                        </svg>
                                       <span class="ms-2">{{__('messages.helpdesk')}}</span>
                                    </a>
                                 </li>
                                 @endif
                                 <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                       @csrf
                                       <a class="dropdown-item" href="javascript:void(0)" onclick="event.preventDefault();
                                          this.closest('form').submit();">
                                          <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                             <g id="Logout">
                                                <path id="Fill 1" fill-rule="evenodd" clip-rule="evenodd"
                                                   d="M6.54616 14.1666H3.28883C1.6595 14.1666 0.333496 12.8406 0.333496 11.2099V3.79059C0.333496 2.15992 1.6595 0.833252 3.28883 0.833252H6.53883C8.1695 0.833252 9.49616 2.15992 9.49616 3.79059V4.41192C9.49616 4.68792 9.27216 4.91192 8.99616 4.91192C8.72016 4.91192 8.49616 4.68792 8.49616 4.41192V3.79059C8.49616 2.71059 7.61816 1.83325 6.53883 1.83325H3.28883C2.21083 1.83325 1.3335 2.71059 1.3335 3.79059V11.2099C1.3335 12.2893 2.21083 13.1666 3.28883 13.1666H6.54616C7.62083 13.1666 8.49616 12.2919 8.49616 11.2173V10.5886C8.49616 10.3126 8.72016 10.0886 8.99616 10.0886C9.27216 10.0886 9.49616 10.3126 9.49616 10.5886V11.2173C9.49616 12.8439 8.17216 14.1666 6.54616 14.1666Z"
                                                   fill="currentColor" />
                                                <g id="Group 5">
                                                   <mask id="mask0_880_191" style="mask-type:luminance"
                                                      maskUnits="userSpaceOnUse" x="4" y="7" width="11" height="1">
                                                      <path id="Clip 4" fill-rule="evenodd" clip-rule="evenodd"
                                                         d="M4.99731 7H14.0246V8H4.99731V7Z" fill="white" />
                                                   </mask>
                                                   <g mask="url(#mask0_880_191)">
                                                      <path id="Fill 3" fill-rule="evenodd" clip-rule="evenodd"
                                                         d="M13.5246 8H5.49731C5.22131 8 4.99731 7.776 4.99731 7.5C4.99731 7.224 5.22131 7 5.49731 7H13.5246C13.8006 7 14.0246 7.224 14.0246 7.5C14.0246 7.776 13.8006 8 13.5246 8Z"
                                                         fill="currentColor" />
                                                   </g>
                                                </g>
                                                <g id="Group 8">
                                                   <mask id="mask1_880_191" style="mask-type:luminance"
                                                      maskUnits="userSpaceOnUse" x="11" y="5" width="4" height="5">
                                                      <path id="Clip 7" fill-rule="evenodd" clip-rule="evenodd"
                                                         d="M11.073 5.05664H14.0246V9.94381H11.073V5.05664Z" fill="white" />
                                                   </mask>
                                                   <g mask="url(#mask1_880_191)">
                                                      <path id="Fill 6" fill-rule="evenodd" clip-rule="evenodd"
                                                         d="M11.5728 9.94381C11.4448 9.94381 11.3162 9.89514 11.2188 9.79647C11.0242 9.60047 11.0248 9.28447 11.2202 9.08981L12.8162 7.49981L11.2202 5.91047C11.0248 5.71581 11.0235 5.39981 11.2188 5.20381C11.4135 5.00781 11.7295 5.00781 11.9255 5.20247L13.8775 7.14581C13.9722 7.23914 14.0248 7.36714 14.0248 7.49981C14.0248 7.63247 13.9722 7.76047 13.8775 7.85381L11.9255 9.79781C11.8282 9.89514 11.7002 9.94381 11.5728 9.94381Z"
                                                         fill="currentColor" />
                                                   </g>
                                                </g>
                                             </g>
                                          </svg>
                                          <span class="ms-2"> {{__('messages.logout')}}</span>
                                       </a>
                                    </form>
                                 </li>
                              </ul>
                           </li>
                        @endif
                      @endif
                   </ul>
                </div>
             </div>
          </div>
       </div>
    </nav>
 </header>

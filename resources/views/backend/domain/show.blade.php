@extends('backend.layouts.master')

@section('content')
<div class="content">
    <div class="qb_cdhttks_page">
        <h2 class="title_page">Chi tiết tên miền</h2>
        <div class="cdhttks_ct">
            <div class="info-domain">
                <div class="title">
                    <h3 style="color: black !important">Thông tin tên miền</h3>
                </div>
                <table>
                    <tbody>
                        <tr>
                            <td class="t_nsd_nc"><strong>Tên miền :</strong></td>
                            <td class="t_nsd_nc_td"><strong>{{ $domain['domain_name'] }}</strong></td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Ngày đăng ký :</strong></td>
                            <td class="t_nsd_nc_td"><strong>{{ \Carbon\Carbon::parse($domain['infodomain']['created_date'])->format('Y-m-d H:i:s') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Ngày hết hạn :</strong></td>
                            <td class="t_nsd_nc_td"><strong>{{ \Carbon\Carbon::parse($domain['infodomain']['expiration_date'])->format('Y-m-d H:i:s') }}</strong></td>
                        </tr>

                        <tr>
                            <td class="t_nsd_nc"><strong>Nameservers :</strong></td>
                            <td class="t_nsd_nc_td"><strong>{{ $domain['ns']['adddomain1'] }}</strong></td>
                        </tr>

                        @if($domain['ns']['adddomain2'])
                            <tr>
                                <td class="t_nsd_nc"><strong>Nameservers :</strong></td>
                                <td class="t_nsd_nc_td"><strong>{{ $domain['ns']['adddomain2'] }}</strong></td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="info-reg-domain">
                <div class="title">
                    <h3>Thông tin chủ thể tên miền</h3>
                </div>
                <table>
                    <tbody>
                        <tr>
                            <td class="t_nsd_nc"><strong>Kiểu chủ thể :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['role'] == 'R' ? 'TỔ CHỨC' : 'CÁ NHÂN' }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Tên chủ thể :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['RegLname'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Mã số thuế/CMT :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['RegOwnerid'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Địa chỉ :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['RegStreet1'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Điện thoại di động :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['RegPhone'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Email :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['RegEmail'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="info-adm-domain">
                <div class="title">
                    <h3>Thông tin người quản lý (admin)</h3>
                </div>
                <table>
                    <tbody>
                        <tr>
                            <td class="t_nsd_nc"><strong>Họ tên :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['AdmLname'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>CMT :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['AdmOwnerid'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Địa chỉ :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['AdmStreet1'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Tỉnh thành :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['AdmCity'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Quốc gia :</strong></td>
                            <td class="t_nsd_nc_td">VIET NAM</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Điện thoại di động :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['AdmPhone'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Email :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['AdmEmail'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Giới tính :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['AdmGender'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Ngày sinh :</strong></td>
                            <td class="t_nsd_nc_td">{{ \Carbon\Carbon::parse($domain['AdmBirthday'])->format('Y-m-d') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="info-tech-domain">
                <div class="title">
                    <h3>Thông tin người quản lý kỹ thuật (tech)</h3>
                </div>
                <table>
                    <tbody>
                        <tr>
                            <td class="t_nsd_nc"><strong>Họ tên :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['TecLname'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>CMT :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['TecOwnerid'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Địa chỉ :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['TecStreet1'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Tỉnh thành :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['TecCity'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Quốc gia :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['TecCc'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Điện thoại di động :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['TecPhone'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Email :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['TecEmail'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Giới tính :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['TecGender'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Ngày sinh :</strong></td>
                            <td class="t_nsd_nc_td">{{ \Carbon\Carbon::parse($domain['TecBirthday'])->format('Y-m-d') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="info-bil-domain">
                <div class="title">
                    <h3>Thông tin người thanh toán (billing)</h3>
                </div>
                <table>
                    <tbody>
                        <tr>
                            <td class="t_nsd_nc"><strong>Họ tên :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['BilLname'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>CMT :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['BilOwnerid'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Địa chỉ :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['BilStreet1'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Tỉnh thành :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['BilCity'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Quốc gia :</strong></td>
                            <td class="t_nsd_nc_td">VIET NAM</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Điện thoại di động :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['BilPhone'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Email :</strong></td>
                            <td class="t_nsd_nc_td">{{ $domain['BilEmail'] }}</td>
                        </tr>
                        <tr>
                            <td class="t_nsd_nc"><strong>Ngày sinh :</strong></td>
                            <td class="t_nsd_nc_td">{{ \Carbon\Carbon::parse($domain['BilBirthday'])->format('Y-m-d') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>

    /* Page title */
    .title_page {
        text-align: center;
        color: #000000;
        font-size: 27px;
        font-weight: 800;
        margin-bottom: 20px;
    }

    /* Domain information section */
    .cdhttks_ct {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    /* Section titles */
    .cdhttks_ct .title h3 {
        color: #333;
        border-bottom: 2px solid #007BFF;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }

    /* Table styles */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    td {
        padding: 10px;
        border: 1px solid #ddd;
        vertical-align: top;
    }

    .t_nsd_nc {
        font-weight: bold;
        color: #555;
    }

    /* Button styles */
    .btn {
        background-color: #007BFF;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn:hover {
        background-color: #0056b3;
    }

    /* Centering the download button */
    .center {
        text-align: center;
        margin-top: 20px;
    }
</style>
@endpush

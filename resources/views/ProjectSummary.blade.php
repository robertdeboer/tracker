<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-us">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Project {{$project->name}} Summary</title>
    <style type="text/css">
        #outlook a {
            padding: 0;
        }

        body {
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            margin: 0;
            padding: 0;
        }

        .ExternalClass {
            width: 100%;
        }

        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td,
        .ExternalClass div {
            line-height: 100%;
        }

        #backgroundTable {
            margin: 0;
            padding: 0;
            width: 100% !important;
            line-height: 100% !important;
        }

        img {
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        a img {
            border: none;
        }

        .image_fix {
            display: block;
        }

        p {
            margin: 1em 0;
        }

        h1, h2, h3, h4, h5, h6 {
            color: black !important;
        }

        h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {
            color: blue !important;
        }

        h1 a:active, h2 a:active, h3 a:active, h4 a:active, h5 a:active, h6 a:active {
            color: red !important;
        }

        h1 a:visited, h2 a:visited, h3 a:visited, h4 a:visited, h5 a:visited, h6 a:visited {
            color: purple !important;
        }

        table td {
            border-collapse: collapse;
        }

        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        a {
            color: orange;
        }

        @media only screen and (max-device-width: 480px) {
            a[href^="tel"], a[href^="sms"] {
                text-decoration: none;
                color: black; /* or whatever your want */
                pointer-events: none;
                cursor: default;
            }

            .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
                text-decoration: default;
                color: orange !important; /* or whatever your want */
                pointer-events: auto;
                cursor: default;
            }
        }

        /* More Specific Targeting */
        @media only screen and (min-device-width: 768px) and (max-device-width: 1024px) {
            /* You guessed it, ipad (tablets, smaller screens, etc) */
            /* Step 1a: Repeating for the iPad */
            a[href^="tel"], a[href^="sms"] {
                text-decoration: none;
                color: blue; /* or whatever your want */
                pointer-events: none;
                cursor: default;
            }

            .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
                text-decoration: default;
                color: orange !important;
                pointer-events: auto;
                cursor: default;
            }
        }

        @media only screen and (-webkit-min-device-pixel-ratio: 2) {
            /* Put your iPhone 4g styles in here */
        }

        /* Following Android targeting from:
        http://developer.android.com/guide/webapps/targeting.html
        http://pugetworks.com/2011/04/css-media-queries-for-targeting-different-mobile-devices/  */
        @media only screen and (-webkit-device-pixel-ratio: .75) {
            /* Put CSS for low density (ldpi) Android layouts in here */
        }

        @media only screen and (-webkit-device-pixel-ratio: 1) {
            /* Put CSS for medium density (mdpi) Android layouts in here */
        }

        @media only screen and (-webkit-device-pixel-ratio: 1.5) {
            /* Put CSS for high density (hdpi) Android layouts in here */
        }

        .border {
            border: 1px solid black;
        }
    </style>
    <!-- Targeting Windows Mobile -->
    <!--[if IEMobile 7]>
    <style type="text/css">
    </style>
    <![endif]-->
    <!--[if gte mso 9]>
    <style>
        /* Target Outlook 2007 and 2010 */
    </style>
    <![endif]-->
</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" id="backgroundTable">
    <caption></caption>
    <tr>
        <td>
            <table cellpadding="0" cellspacing="0" border="0" align="center">
                <caption></caption>
                <tbody>
                <tr>
                    <td colspan="4">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="4" align="center" valign="top"
                        style="font-size: x-large; font-weight: bold; padding: 5px;">{{$project->name}}</td>
                </tr>
                <tr>
                    <td width="200" align="center" valign="top"
                        style="background: lightgray; border: 1px solid white; padding: 5px;">
                        Total Hours<br/><b>{{$project->orders->sum('hours')}}</b>
                    </td>
                    <td width="200" align="center" valign="top"
                        style="background: lightgray; border: 1px solid white; padding: 5px;">
                        Hours
                        Remaining<br/><b>{{$project->orders->sum('hours') - $project->time_entries->sum('hours')}}</b>
                    </td>
                    <td width="200" align="center" valign="top"
                        style="background: lightgray; border: 1px solid white; padding: 5px;">
                        Work Items<br/><b>{{$project->work_items->count()}}</b>
                    </td>
                    <td width="200" align="center" valign="top"
                        style="background: lightgray; border: 1px solid white; padding: 5px;">
                        Open Items<br/><b>{{$project->work_items->where('is_open','=',true)->count()}}</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="4">
                        <table width="100%">
                            <caption></caption>
                            <tbody>
                            <tr>
                                <td colspan="{{count($chart['labels'])}}" align="center"
                                    style="font-weight: bold; font-size: large;">Hours
                                </td>
                            </tr>
                            <tr class="border" style="height: 600px;" valign="bottom" align="center">
                                @foreach($chart['data'] as $data)
                                    @if($data <= 0)
                                        <td>{{$data}}</td>
                                    @else
                                        <td>
                                            <div style="background-color: lightblue; height: {{(($data / max($chart['data'])) * 600)}}px;">
                                                {{$data}}
                                            </div>
                                        </td>
                                    @endif
                                @endforeach
                            </tr>
                            <tr>
                                @foreach($chart['labels'] as $label)
                                    <td class="border" align="center">{{$label}}</td>
                                @endforeach
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="4" class="border">
                        <table cellpadding="0" cellspacing="0" border="0" width="100%">
                            <caption></caption>
                            <thead>
                            <tr style="background-color: lightgray;">
                                <th align="left">Work Item</th>
                                <th align="left">IPS Title</th>
                                <th align="left">Date</th>
                                <th align="left">Total Hours</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($project->work_items as $workItem)
                                <tr style="border-bottom: 1px solid black;">
                                    <td width="200" valign="top" style="padding-left: 5px;">{{$workItem->id}}</td>
                                    <td width="200" valign="top">{{$workItem->name}}</td>
                                    <td width="200" valign="top">{{$workItem->start_date->format('yy-m-d')}}</td>
                                    <td width="200" valign="top">{{$workItem->time_entries->sum('hours')}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfooter>
                                <tr style="background-color: lightgray;">
                                    <td colspan="3"></td>
                                    <td>Total Hours: <b>{{$total}}</b>&nbsp;&nbsp;Rebated: <b>{{$rebated}}</b></td>
                                </tr>
                            </tfooter>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>
</body>
</html>

<h5>General options</h5>

<h6>-a, --aggregation=field[,...]</h6>
<p>Aggregate flow records together by any number of fields. Option argument is comma separated list of fields, at least one field is mandatory. Set of supported fields depends on your version of libnf library. libnf comes with libnf-info executable, which prints list of implemented fields and their meaning.</p>

<h6>-f, --filter=filter</h6>
<p>Process only filter matching records. A filter can be specified as a text string, the syntax and semantics of the string are as described by nfdump(1). A filter string is compiled by libnf, which currently uses nfdump filtering engine by default. This will change in future versions and libnf will use its own filtering engine.</p>

<h6>-l, --limit=limit</h6>
<p>Limit the number of records to print. Option argument is a natural number, special value 0 disables the limit.<br><br>
Keep in mind that with enabled aggregation, this doesn't reduce the number of processed records. During the aggregation, all filter matching records have to be processed for the sake of result correctness.  On the other hand, without aggregation, this may vastly reduce the number of processed records and therefore reduce the query running time.</p>

<h6>-o, --order=field[#direction]</h6>
<p>Set record sort order. Sort key is specified by field. Sort direction is either asc or desc string, which are abbreviations for ascending and descending direction, respectively.</p>

<h6>-s, --statistic=statistic</h6>
<p>Shortcut for aggregation (-a), sort (-o) and record limit (-l). Option argument string statistic is composed of -a argument, optional slash followed by -o argument. Formally written statistic is aggregation_field[,...]/[sort_field[#sort_direction]].<br><br>
If sort_field is not present, flows is default sort key. Default record limit is 10 and may by changed by an explicit -l option.</p>

<h6>-t, --time-point=time_spec</h6>
<p>time_spec string should contain one or more white-space separated time specifiers. Time specifier is a representation of a
date, time, special value or UTC flag. Supported date formats are  ISO  (YYYY-MM-DD), European (DD.MM.YYYY) and American
(MM/DD/YYYY), time format is only one (hh:mm) and also Unix time (number of seconds that have elapsed since 00:00:00 UTC) is supported. Special value is either the name of the day of the week or the month name, both according to the current locale, in abbreviated form or the full name. The UTC flag is either U or UTC string. If the UTC flag is present, all the time specifiers are assumed to be in UTC instead of local time.<br><br>
If no date is given, today is assumed if the given hour is lesser than the current hour and yesterday is assumed if it is more. If no time is given, midnight is assumed.  If only the weekday is given, today is assumed if the given day is less or equal to the current day and last week if it is more.  If only the month is given, the current year is assumed if the given month is less or equal to the current month and last year if it is more and no year is given.</p>

<h6>-T, --time-range=begin[#end]</h6>
<p>Process only flow files from begin to the end time range. Both begin and end are time_spec strings. If end is not provided, current time is assumed as range end. All other aspects that was mentioned for the time point option (-t) apply also for this option.<br><br>
If given begin and end times are not aligned to the flow file rotation intervals (which is usually 5 minutes), alignment is automaticly perfomed.  Beginning time is aligned to the beginning of the rotation interval, ending time is aligned to the ending of the rotation interval</p>




<h5>Controlling the output</h5>

<h6>--output-items=item_list</h6>
<p>Set output items. item_list is comma-separated list of the output items. Output items are records (r), processed-records-summary (p) and metadata-summary (m), you can use a full names or the abbreviated forms. records means result of the query, processed-records-summary is the summary of the records that were processed during the query (i.e. filter matching records). At the beginning of each flow file, there is a header containing sums of the flows, pkts and bytes fields of all the records in that file.  Those sums are further divided according to the transport protocols TCP, UDP, ICMP. metadata-summary output item will read and print those metadata counters. Using metadata-summary as a single output item is very fast and efficent.<br><br>
Default value of item_list for pretty output is records,processed-records-summary, for CSV it contains only records.</p>

<h6>--output-format=format</h6>
<p>Set output (print) format.  format is either pretty or csv. pretty will create nice, human readable output, with fields formatted into columns.  It is the default option. Data conversions are all set to the most human readable form (timestamps converted into broken-down time strings, TCP flags converted into string, ...). On the other hand, csv will create machine readable output suitable for post-processing. It is a standard comma separated values format, where all data conversions are set to the most machine readable form (timestamps printed as integers, TCP flags printed as a integers, ...).</p>

<h6>--output-ts-conv=timestamp_conversion</h6>
<p>Set timestamp output conversion format. timestamp_conversion is either none or custom format string.<br><br>
With none conversion, raw timestamp integer is printed. Timestamp is composed from Unix time (number of seconds that have elapsed since 1.1.1970 UTC) enhanced with milliseconds (seconds are multiplied by 1000 and milliseconds are added). For example 1445405280123 means 21.10.2015 7:28, 123 ms.<br><br>
Custom format string is simply passed as format string to the strftime() function. Default string for pretty print is '%F %T'. Dot and milliseconds are always appended.</p>

<h6>--output-ts-localtime</h6>
<p>Convert timestamps to local time. Timestamps stored in flow records are in UTC. This option will convert them to the user's specified timezone (by localtime() function) before output conversion is performed.</p>

<h6>--output-volume-conv=volume_conversion</h6>
<p>Set volume output conversion format.  Volume fields  are bytes, pkts, outbytes, outpkts, flows, bsp, pps and bpp.  This conversion is also applied to the summary.<br><br>
volume_conversion is one of none, metric-prefix or binary-prefix. none conversion will print raw integer or double. Following will prepend standard unit prefix to indicate multiples of the unit.  The prefixes of the metric system such as kilo and mega, represent multiplication by powers of ten. In information technology it is common to use binary prefixes such as kibi and mebi, which are based on powers of two. For example 150000 will be converted to 150.0 k using metric-prefix and to 146.4 Ki using binary-prefix.</p>

<h6>--output-tcpflags-conv=TCP_flags_conversion</h6>
<p>Set TCP flags output conversion format. TCP_flags_conversion is either none or str. TCP flags are composed of 8 bits: CWR, ECE, URG, ACK, PSH, RST, SYN and FIN.<br><br>
Using none conversion, raw integer is printed. Using str conversion, flags are converted into human readable string composing of 8 characters. Each character represents one bit, order is preserved (CWR is first, FIN is last). If bit is set, character is set to the first letter of bit's name.  If bit is unset, character is set to the dot symbol. For example C.UA..SF means that CWR, URG, ACK, SYN and FIN bits are set, others are unset.</p>

<h6>--output-addr-conv=IP_address_conversion</h6>
<p>Set IP address output conversion format. IP_address_conversion is either none or str.  IP address is either IPv4 or IPv6 address.<br><br>
With  none  conversion,  IP  address  is  converted  to UINT[0]:UINT[1]:UINT[2]:UINT[3].  If IPv4 is present, first three UINTs are zero. With str conversion, inet_ntop() function is used to convert binary representation to string.</p>

<h6>--output-proto-conv=IP_protocol_conversion</h6>
<p>Set IP protocol output conversion format. IP protocol is one octet long field in the IP header which defines the protocol used in the data portion of the IP datagram (usually TCP or UDP).  The Internet Assigned Numbers Authority maintains a list of IP protocol numbers.<br><br>
IP_protocol_conversion is either none or str. Using none conversion will print raw integer. Using str conversion will print IP protocol name, as defined by IANA.</p>

<h6>--output-duration-conv=duration_conversion</h6>
<p>Set duration conversion format. duration is field calculated by end - start.  duration_conversion is either none or str. Using none, raw integer is printed. Using str, duration is converted into HH:MM:SS.MS string.</p>

<h6>--fields=field[,...]</h6>
<p>Set the list of printed fields. Format of the argument is the same as for -a option.  Without enabled aggregation, default fields  are  first, pkts, bytes, srcip, dstip, srcport, dst port and proto, with aggregation enabled, default fields are duration, flows, pkts, bytes, bps, pps and bpp plus aggregation keys.<br><br>
Without aggregation, you can add every valid field.  Just keep in mind, that the more fields are present, the more data have to processed and transferred from slaves to master.  With nfdump file format, it isn't possible to determine if the field is present in the flow record or not. If the desired field isn't present, it will be printed as zero (or what the specified output conversion creates from zero).<br><br>
With aggregation, this can be a little tricky. You can add only some fields without actually making the field aggregation key. Those  fields  are  first, last, received, bytes, pkts, outbytes, outpkts, flows, tcpflags, eventtime, duration, bps, pps and bpp. If any other field is present in the list, it will be used as aggregation key.</p>

<h6>--progress-bar-type=progress_bar_type</h6>
<p>Set progress bar type. Progress is calculated with resolution of one file. This may be inaccurate if records are unevenly spread among files.<br><br>
progress_bar_type is one of none, total, perslave or json. none will disable progress bar, total will print only total progress (enabled by default), perslave will print per slave progress together with total progress, json will print per slave progress formatted as a JSON.</p>

<h6>--progress-bar-dest=progress_bar_destination</h6>
<p>Set progress bar destination.  There are two special values: stdout and stderr (which is also default).  Every other value will be treated as a filename and fdistdump will continually rewrite this file with the current progress.</p>



<h5>Other options</h5>

<h6>--no-fast-topn</h6>
<p>Disable fast top-N algorithm. fdistdump uses this algorithm for statistic (or top-N) queries. This option shouldn't influence results in any way, it is just an optimization.  It should reduce amount of data transferred between master and slave(s). There are three conditions that have to be met to make this algorithm work:
<ol>
	<li>it is not disabled by this option</li>
	<li>record limit is enabled (-l)</li>
	<li>sort  key  (-o)  is  one  of  traffic volume fields (bytes, pkts, outbytes, outpkts and flows).</li>
</ol>
</p>
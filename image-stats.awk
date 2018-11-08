#! /bin/gawk -f
# Run with 'ls -l storage/respondent-photos | gawk -f image-stats.awk'
BEGIN {
    min = 10000000;
}
{
    sum += $5;
    if ($5 > max) max = $5;
    if ($5 <= min) min = $5;
    n++;
}
END {
    print "total " sum " average " sum / n " max " max " min " min " out of " n
}
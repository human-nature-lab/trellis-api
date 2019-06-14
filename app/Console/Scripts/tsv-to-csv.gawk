#! /bin/gawk -f
# Run with `cat name.tsv | gawk -f tsv-to-csv.gawk > outfile.csv`
#   or `mysql -b -e "select * from user" db_name | gawk -f tsv-to-csv.gawk > outfile.csv`

BEGIN { FS="\t"; OFS="," }

{
  rebuilt=0
  for(i=1; i<=NF; ++i) {
    if ($i ~ /,/ && $i !~ /^".*"$/) {
      if ($i ~ /"/) {
        gsub(/"/, "\"\"", $i)
      }
      $i = "\"" $i "\"";
      rebuilt = 1;
    }
  }
  if (!rebuilt) { $1=$1 }
  print
}
#!/usr/bin/env bash
echo "Analyzing file sizes"
ls -l storage/respondent-photos | gawk -f image-stats.awk

echo "Compressing images"
for filepath in storage/respondent-photos/*.jpg; do
    convert -strip -interlace Plane -quality 20% $filepath storage/respondent-photos-converted/$(basename $filepath)
done

echo "Analyzing output file sizes"
ls -l storage/respondent-photos-converted | gawk -f image-stats.awk
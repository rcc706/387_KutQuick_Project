import sys
import subprocess
import os
from moviepy.editor import VideoFileClip, concatenate_videoclips

input_path = sys.argv[1] # input file path
out_path = sys.argv[2]   # output file path
threshold = sys.argv[3]  # silence threshold in decibels (i.e. -30 dB)
duration = sys.argv[4]   # minimum duration of silence to detect (i.e. 0.8)

# optional ease factor to add time at the beginning of silence (default 0.2)
try:
    ease = float(sys.argv[5])
except IndexError:
    ease = 0.2

minimum_duration = 1

# use ffmpeg for silence detection and generate timestamps 
def generate_timestamps(input_path, threshold, duration):
    # ffmpeg command to detect silence    
    command = [
        "ffmpeg",
        "-hide_banner", 
        "-vn", 
        "-i", input_path,
        "-af", f"silencedetect=n={threshold}dB:d={duration}",
        "-f", "null", 
        "-"
    ]
    
    # run the command
    result = subprocess.run(command, stderr=subprocess.PIPE, text=True)
    
    # capture the output, it contains silence detection info
    output = result.stderr

    # parse the output to extract the silence_end and silence_duration
    timestamps = []
    for line in output.splitlines():
        if "silence_end" in line:
            parts = line.split()
            silence_end = parts[4]  # silence end time
            silence_duration = parts[7]  # silence duration
            timestamps.append(f"{silence_end} {silence_duration}")
    
    return timestamps

def main():
    count = 0  # clip counter
    last = 0   # last timestamp processed

    timestamps = generate_timestamps(input_path, threshold, duration)  # get silence timestamps
    print(f"Timestamps: {timestamps}")

    video = VideoFileClip(input_path) # load video file
    full_duration = video.duration    # set total duration of video

    clips = []
    for times in timestamps:
        end,dur = times.strip().split() # extract the tend time and duration of silence
        print(f"End: {end}, Duration: {dur}")

        to = float(end) - float(dur) + ease # adjust end time with ease

        start = float(last) # start of clip is the last timestamp
        clip_duration = float(to) - start

        print(f"Clip Duration: {clip_duration} seconds")

        # skip clips that are too short
        if clip_duration < minimum_duration:
            continue

        # skip last clip if too close to the end of the video
        if full_duration - to < minimum_duration:
            continue

        print(f"Clip {count} (Start: {start}, End: {to})")
        clip = video.subclip(start, to) # extract clip from the video
        clips.append(clip)
        last = end
        count += 1

    # no clips found
    if not clips:
        print("No silence detected, exiting...")
        return

    # add the last clip if there is enough duration
    if full_duration - float(last) > minimum_duration:
        print("Clip {count} (Start: {last}, End: EOF)")
        clips.append(video.subclip(last))

    # concatenate all clips into final video with moviepy
    processed_video = concatenate_videoclips(clips)

    # write processed video to output file
    processed_video.write_videofile(
        out_path,
        fps=60,
        preset='ultrafast', # encoding speed
        codec='libx264',    # video codec
        audio_codec='aac'   # audio codec
    )

    video.close()

main()


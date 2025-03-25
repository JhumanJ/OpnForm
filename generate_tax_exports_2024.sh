#!/bin/bash

# Script to generate tax exports for each month of 2024 in parallel

# Default number of concurrent processes
MAX_CONCURRENT=4

# Process command line arguments
while getopts "c:" opt; do
  case $opt in
    c) MAX_CONCURRENT=$OPTARG ;;
    *) echo "Usage: $0 [-c max_concurrent_processes]" >&2
       exit 1 ;;
  esac
done

# Ensure valid concurrent processes value
if ! [[ "$MAX_CONCURRENT" =~ ^[1-9][0-9]*$ ]]; then
    echo "Error: Invalid number of concurrent processes: $MAX_CONCURRENT"
    echo "Please provide a positive integer."
    exit 1
fi

# Function to log messages with timestamp
log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

# Ensure we're in the Laravel API directory
cd api || { log "Error: API directory not found"; exit 1; }

log "Starting tax export generation for all months of 2024 (max $MAX_CONCURRENT concurrent processes)"

# Create a directory for the logs
mkdir -p tax_export_logs

# Create a temporary directory for status files
tmp_dir=$(mktemp -d)
trap 'rm -rf "$tmp_dir"' EXIT

# Function to run export for a specific month
run_export() {
    month=$1
    month_num=$(printf "%02d" $month)
    year=2024
    
    # Calculate start and end dates for the month
    start_date="${year}-${month_num}-01"
    
    # Determine the end date based on the month
    case $month in
        2)  # February (considering leap year 2024)
            end_date="${year}-${month_num}-29"
            ;;
        4|6|9|11)  # April, June, September, November
            end_date="${year}-${month_num}-30"
            ;;
        *)  # January, March, May, July, August, October, December
            end_date="${year}-${month_num}-31"
            ;;
    esac
    
    log_file="tax_export_logs/export-${year}-${month_num}.log"
    status_file="${tmp_dir}/status_${month}.txt"
    
    log "Starting tax export for ${year}-${month_num}..."
    
    # Run the Laravel command for this month and log the output
    {
        echo "================ EXPORT START: $(date) ================"
        php artisan stripe:generate-stripe-export --start-date=$start_date --end-date=$end_date
        exit_code=$?
        echo "================ EXPORT END: $(date) ================"
        echo "Exit code: $exit_code"
        
        if [ $exit_code -ne 0 ]; then
            echo "ERROR: Export failed with exit code $exit_code"
        else
            echo "SUCCESS: Export completed successfully"
        fi
        
        # Write status to file for the parent process to read
        echo "$exit_code" > "$status_file"
    } > "$log_file" 2>&1
    
    if [ $exit_code -ne 0 ]; then
        log "ERROR: Export for ${year}-${month_num} failed with exit code $exit_code"
    else
        log "Successfully completed tax export for ${year}-${month_num}"
    fi
}

# Track running processes
pids=()
months=()

# Export each month with limited concurrency
for month in {1..12}; do
    # Wait if we've reached the maximum number of concurrent processes
    while [ ${#pids[@]} -ge $MAX_CONCURRENT ]; do
        # Check if any processes have completed
        for i in "${!pids[@]}"; do
            if ! kill -0 ${pids[$i]} 2>/dev/null; then
                # Process has completed, check its status
                if [ -f "${tmp_dir}/status_${months[$i]}.txt" ]; then
                    status=$(cat "${tmp_dir}/status_${months[$i]}.txt")
                    if [ "$status" -ne 0 ]; then
                        log "Process for month ${months[$i]} completed with error: $status"
                    fi
                fi
                # Remove from arrays
                unset pids[$i]
                unset months[$i]
                # Reindex arrays
                pids=("${pids[@]}")
                months=("${months[@]}")
                break
            fi
        done
        sleep 1
    done
    
    # Start the export process in the background
    run_export $month &
    pid=$!
    pids+=($pid)
    months+=($month)
    
    log "Started export for month $month (PID: $pid)"
    
    # Optional: Add a small delay to avoid overwhelming the system
    sleep 0.5
done

# Wait for all background processes to complete
log "Waiting for all export processes to complete..."
wait

# Collect results
failed_months=()
for month in {1..12}; do
    if [ -f "${tmp_dir}/status_${month}.txt" ]; then
        status=$(cat "${tmp_dir}/status_${month}.txt")
        if [ "$status" -ne 0 ]; then
            failed_months+=($month)
        fi
    else
        # Status file missing - consider it a failure
        log "WARNING: No status file found for month $month. Considering it failed."
        failed_months+=($month)
    fi
done

# Check if any months failed
if [ ${#failed_months[@]} -gt 0 ]; then
    log "WARNING: Exports for the following months failed: ${failed_months[*]}"
    log "Please check the corresponding log files for details."
    exit_code=1
else
    log "All tax exports completed successfully for 2024"
    exit_code=0
fi

log "All exports have been processed. Check tax_export_logs/ directory for detailed logs."
exit $exit_code 
<template>
    <div class="relative">
        <video id="webcam" autoplay playsinline :class="{'hidden':!isCapturing}"></video>
        <canvas id="canvas" :class="{'hidden':!capturedImage}"></canvas>
        
        <div class="absolute inset-x-0 grid place-content-center bottom-2">
            <div class=" p-2 px-4 flex items-center justify-center text-xs space-x-2" v-if="isCapturing">
                <span class="cursor-pointer  rounded-full w-14 h-14 border-2 border-white grid place-content-center" @click="takeSnapshot">
                <span class="cursor-pointer bg-gray-100 rounded-full w-10 h-10 grid place-content-center">
                </span>
                </span>
                <span class="text-white cursor-pointer" @click="cancelCamera">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </span>
            </div>

            <div class="bg-black rounded-full p-1 px-2 flex items-center justify-center text-xs space-x-4" v-if="capturedImage">
                <span class="text-amsber-600 cursor-pointer" @click="openCameraUpload">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>

                </span>
                
                
                <span class="text-lime-600 cursor-pointer w-8 h-8 rounded-full bg-gray-100 grid place-content-center" @click="processCapturedImage">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </span>
                
                <span class="text-resd-600 cursor-pointer"  @click="cancelCamera">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </span>
            </div>
        </div>
    </div>
</template>

<script>
import Webcam from 'webcam-easy';
export default {
    name: 'FileInput',
    data: () => ({
        webcam:null,
        isCapturing: false,
        capturedImage: null,
    }),
    computed:{
        videoDisplay(){
            return this.isCapturing ? '' : 'hidden';
        },
        canvasDisplay(){
            return (!this.isCapturing && this.capturedImage) ? '' : 'hidden'
        }
    },
    mounted(){
        const webcamElement = document.getElementById('webcam');
        const canvasElement = document.getElementById('canvas');
        this.webcam = new Webcam(webcamElement, 'user', canvasElement);
        this.openCameraUpload()
        
    },

    methods: {
        openCameraUpload() {
            this.isCapturing = true;   
            this.capturedImage = null;     
            this.webcam.start()
                .then(result => {
                    console.log("webcam started");
                })
                .catch(err => {
                    console.log('err', err);
                });
        },

        takeSnapshot() {
            var picture = this.webcam.snap();
            this.isCapturing = false;
            this.webcam.stop()
            this.capturedImage = picture;
        },
        cancelCamera() {
            this.isCapturing = false;
            this.capturedImage = null;
            this.webcam.stop()
            this.$emit('stopWebcam')
        },
        processCapturedImage(){
            const byteCharacters = atob(this.capturedImage.split(',')[1]);
            const byteArrays = [];
            for (let offset = 0; offset < byteCharacters.length; offset += 512) {
                const slice = byteCharacters.slice(offset, offset + 512);

                const byteNumbers = new Array(slice.length);
                for (let i = 0; i < slice.length; i++) {
                    byteNumbers[i] = slice.charCodeAt(i);
                }

                const byteArray = new Uint8Array(byteNumbers);
                byteArrays.push(byteArray);
            }

            // Create Blob from binary data
            const blob = new Blob(byteArrays, { type: 'image/png' });
            const  filename = Date.now()
            // Create a File object from the Blob
            const file = new File([blob], `${filename}.png`, { type: 'image/png' });
            this.$emit('uploadImage', file)
        }


    }
}

</script>
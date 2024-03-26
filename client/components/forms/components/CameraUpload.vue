<template>
    <div class="relative border">
        <video id="webcam" autoplay playsinline :class="[{ 'hidden': !isCapturing },theme.fileInput.cameraInput]" width="1280" height="720"></video>
        <canvas id="canvas" :class="{ 'hidden': !capturedImage }"></canvas>
        <div v-if="cameraPermissionStatus === 'allowed'" class="absolute inset-x-0 grid place-content-center bottom-2">
            <div class=" p-2 px-4 flex items-center justify-center text-xs space-x-2" v-if="isCapturing">
                <span class="cursor-pointer  rounded-full w-14 h-14 border-2 grid place-content-center"
                    @click="processCapturedImage">
                    <span class="cursor-pointer bg-gray-100 rounded-full w-10 h-10 grid place-content-center">
                    </span>
                </span>
                <span class="text-white cursor-pointer" @click="cancelCamera">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-8 h-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </span>
            </div>
        </div>
        <div v-else-if="cameraPermissionStatus === 'blocked'"
            class="absolute p-5 top-0 inset-x-0 flex flex-col items-center justify-center space-y-4 text-center rounded border border-gray-400/30 h-full"
            @click="openCameraUpload">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
            </svg>
            <p class="text-center font-bold">
                Allow Camera Permission
            </p>
            <p class="text-xs">You need to allow camera permission before you can take pictures. Go to browser settings to enable camera permission on this page.</p>
            <button class="text-xs p-1 px-2 bg-blue-600 rounded" type="button" @click.stop="cancelCamera">Got it!</button>
        </div>

        <div v-else-if="cameraPermissionStatus === 'loading'"
            class="absolute p-5 top-0 inset-x-0 flex flex-col items-center justify-center space-y-4 text-center rounded border border-gray-400/30 h-full"
            >
            <div class="w-6 h-6">
                <Loader />
            </div>
        </div>
        <div v-else
            class="absolute p-5 top-0 inset-x-0 flex flex-col items-center justify-center space-y-4 text-center rounded border border-gray-400/30 h-full"
            @click="openCameraUpload">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M12 18.75H4.5a2.25 2.25 0 0 1-2.25-2.25V9m12.841 9.091L16.5 19.5m-1.409-1.409c.407-.407.659-.97.659-1.591v-9a2.25 2.25 0 0 0-2.25-2.25h-9c-.621 0-1.184.252-1.591.659m12.182 12.182L2.909 5.909M1.5 4.5l1.409 1.409" />
            </svg>

            <p class="text-center font-bold">
                Camera Device Error
            </p>
            <p class="text-xs">An unknown error occurred when trying to start Webcam device.</p>
            <button class="text-xs p-1 px-2 bg-blue-600 rounded" type="button" @click.stop="cancelCamera">Go back</button>
        </div>

    </div>
</template>

<script>
import Webcam from 'webcam-easy';
import { themes } from '~/lib/forms/form-themes.js'
export default {
    name: 'FileInput',
    props:{
        theme: { type: Object, default: () => themes.default }
    },
    data: () => ({
        webcam: null,
        isCapturing: false,
        capturedImage: null,
        cameraPermissionStatus: 'loading',
    }),
    computed: {
        videoDisplay() {
            return this.isCapturing ? '' : 'hidden';
        },
        canvasDisplay() {
            return (!this.isCapturing && this.capturedImage) ? '' : 'hidden'
        }
    },
    mounted() {
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
                    this.cameraPermissionStatus = 'allowed';
                })
                .catch(err => {
                    console.error(err)
                    if(err.toString()  === 'NotAllowedError: Permission denied'){
                        this.cameraPermissionStatus = 'blocked';
                        return;
                    }
                    this.cameraPermissionStatus = 'unknown';
                });
        },
        cancelCamera() {
            this.isCapturing = false;
            this.capturedImage = null;
            this.webcam.stop()
            this.$emit('stopWebcam')
        },
        processCapturedImage() {
            this.capturedImage = this.webcam.snap();
            this.isCapturing = false;
            this.webcam.stop()
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
            const filename = Date.now()
            // Create a File object from the Blob
            const file = new File([blob], `${filename}.png`, { type: 'image/png' });
            this.$emit('uploadImage', file)
        }


    }
}

</script>

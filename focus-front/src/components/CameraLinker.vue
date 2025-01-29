<template>
    <div padding>
      <div v-if="error" class="text-negative">{{ error }}</div>
      
      <q-btn v-if="!cameras.length" @click="detectCameras" color="primary" label="Detect Cameras" />
      
      <div v-if="cameras.length">
        <q-btn 
          @click="startCameras" 
          v-if="!startCamera3" 
          :disable="streams.length > 0" 
          color="primary" 
          label="Start Cameras" 
        />
        
        <!-- Cameras side by side -->
        <q-gutter-sm class="q-mt-md">
          <div style="position:fixed !important; display: flex; width:70%; " >
            <q-col v-for="(camera, index) in cameras" style="" :key="index" cols="6">
              <q-card >
                <q-card-section>
                  <p style="font-size:30px;">Camera {{ index + 1 }}</p>
                  <video
                    :ref="setVideoRef(index)"
                    autoplay
                    playsinline
                    muted
                    class="fit"
                    style="border: 1px solid #ddd; width: 100%; max-width: 640px; opacity: 0; height: 1px !important; width: 1px !important;"
                  ></video>
                  <canvas :ref="setCanvasRef(index)" style="border: 1px solid #ddd; width: 100%;"></canvas>
                </q-card-section>
              </q-card>
            </q-col>
          </div>
        </q-gutter-sm>
      </div>
  
      <!-- Detected Results -->
      <div v-if="detections.length" style="display: none;" class="q-mt-lg">
        <h2>Detected Plates</h2>
        <q-list >
          <q-item v-for="(detection, index) in detections" :key="index" clickable @click="showImage(detection.image)">
            <q-item-section avatar>
              <q-img :src="detection.image" style="max-width: 50px; border: 1px solid #ddd;" />
            </q-item-section>
            <q-item-section>
              <div>
                <strong>Plate {{ index + 1 }}:</strong> {{ detection.text }}
              </div>
              <div><strong>Color:</strong> {{ detection.color }}</div>
            </q-item-section>
          </q-item>
        </q-list>
      </div>
  
      <!-- Full-Size Image Modal -->
      <q-dialog v-model="imageDialog">
        <q-card>
          <q-card-section>
            <q-img :src="selectedImage" style="max-width: 100%;" />
          </q-card-section>
          <q-card-actions align="right">
            <q-btn flat label="Close" color="primary" @click="imageDialog = false" />
          </q-card-actions>
          <q-btn v-if="selectedImage" @click="convertImageToText" color="secondary" label="Image to Text" />
          <div v-if="extractedText">
            <h4>Extracted Text:</h4>
            <p>{{ extractedText }}</p>
          </div>
        </q-card>
      </q-dialog>
    </div>
  </template>
  
  
  <script>
import { ref, reactive, onMounted } from 'vue';
import * as cocoSsd from '@tensorflow-models/coco-ssd';
import '@tensorflow/tfjs';
import Tesseract from 'tesseract.js';

export default {
  name: 'CameraDetection',
  setup() {
    const cameras = ref([]);
    const streams = reactive({});
    const videoRefs = reactive({});
    const canvasRefs = reactive({});
    const detections = reactive({});
    const error = ref(null);
    const imageDialog = ref(false);
    const selectedImage = ref(null);
    const extractedText = ref(null);

    const detectCameras = async () => {
      try {
        const devices = await navigator.mediaDevices.enumerateDevices();
        cameras.value = devices.filter(device => device.kind === 'videoinput');
      } catch (err) {
        error.value = 'Error detecting cameras: ' + err.message;
      }
    };

    const startCameras = async () => {
      for (const camera of cameras.value) {
        try {
          const stream = await navigator.mediaDevices.getUserMedia({
            video: { deviceId: { exact: camera.deviceId } },
          });
          streams[camera.deviceId] = stream;
        } catch (err) {
          error.value = `Error accessing camera ${camera.label}: ${err.message}`;
        }
      }
    };

    const setVideoRef = (deviceId, ref) => {
      videoRefs[deviceId] = ref;
      if (streams[deviceId] && ref) {
        ref.srcObject = streams[deviceId];
      }
    };

    const setCanvasRef = (deviceId, ref) => {
      canvasRefs[deviceId] = ref;
    };

    const detectObjects = async (deviceId) => {
      if (!videoRefs[deviceId] || !canvasRefs[deviceId]) return;

      const model = await cocoSsd.load();
      const video = videoRefs[deviceId];
      const canvas = canvasRefs[deviceId];
      const ctx = canvas.getContext('2d');

      const detectFrame = async () => {
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const predictions = await model.detect(canvas);
        detections[deviceId] = predictions.filter(pred => pred.class === 'license plate');

        if (video.readyState === 4) {
          requestAnimationFrame(detectFrame);
        }
      };

      detectFrame();
    };

    const showImage = (image) => {
      selectedImage.value = image;
      imageDialog.value = true;
    };

    const convertImageToText = async () => {
      if (!selectedImage.value) return;

      const { data: { text } } = await Tesseract.recognize(selectedImage.value, 'eng');
      extractedText.value = text;
    };

    onMounted(async () => {
      await detectCameras();
      await startCameras();
    });

    return {
      cameras,
      streams,
      videoRefs,
      canvasRefs,
      error,
      detections,
      imageDialog,
      selectedImage,
      extractedText,
      detectCameras,
      startCameras,
      setVideoRef,
      setCanvasRef,
      detectObjects,
      showImage,
      convertImageToText,
    };
  },
};
</script>

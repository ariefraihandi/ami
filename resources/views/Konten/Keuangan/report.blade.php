<!DOCTYPE html>
<html xmlns:v-on="http://www.w3.org/1999/xhtml"
      xmlns:v-bind="http://www.w3.org/1999/xhtml"
      xmlns:v-pre="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  table 
  {
    width: 100%;
    border-collapse: collapse;
  }
  th, td 
  {
    padding: 8px;
    border-bottom: 1px solid #ddd;
    text-align: center;
  }
  th
  {
    background-color: #f2f2f2;
  }
  tr:hover 
  {
    background-color: #f5f5f5;
  }
  .page-break {
    page-break-after: always;
  }
  .image-container {
    margin-top: 45px;
    margin-bottom: 10px;
  }
  body {
    position: relative;
  }
  h3 
  {
    margin-top: 50px; 
    margin-bottom: 5px;
    text-align: left;
  }
  
  .bg-image {
    position: absolute;
    top: 0;
    left: 0;
    /* width: 100%;
    height: 100%; */
    z-index: -1;
    opacity: 0.3;
  }
</style>
</head>
<body style="margin: 0;">  
  <img src="{{ $bgImage }}" alt="Background" class="bg-image" style="position: fixed; top: 0;  width: 115%;  left: -45px;">
  <div style="position: relative;">
    <img src="{{ $logoPath }}" alt="Header Image" style="position: fixed; top: 0; left: -45px; width: 115%; padding: 0;  top: -45px;">
    <div class="image-container" style="position: absolute; top: 45px;">
      <img src="{{ $imagePath }}" alt="Logo" height="70" style="position: fixed; top: 60;" class="logo-img">
    </div>
  </div>
  <br>
  <br>
  <br>
  <br>
  <br>
  <br>
   
</body>
</html>